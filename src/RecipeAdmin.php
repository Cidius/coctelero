<?php
declare(strict_types=1);

namespace App;

use PDO;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/helpers.php';

/**
 * Operaciones de escritura sobre recetas (panel admin).
 * Recipe.php queda solo para lectura publica.
 */
final class RecipeAdmin
{
    /** Metodos validos del ENUM. */
    public const METHODS = ['integrado', 'refrescado_directo', 'batido', 'machacado', 'frozen', 'otro'];

    /** Valores validos de las clasificaciones (Clase 6). */
    public const VOLUMES = ['short', 'medium', 'long'];
    public const MOMENTS = ['aperitivo', 'digestivo', 'all_day'];

    /** Familias para el formulario (con su volumen tipico). */
    public static function families(): array
    {
        return Database::get()
            ->query('SELECT id, name, slug, typical_volume FROM families ORDER BY position ASC, name ASC')
            ->fetchAll();
    }

    /**
     * Listado para el dashboard / la papelera.
     *
     * @return list<array<string,mixed>>
     */
    public static function listAll(bool $trashed = false): array
    {
        $pdo = Database::get();
        $cond = $trashed ? 'r.deleted_at IS NOT NULL' : 'r.deleted_at IS NULL';
        $order = $trashed ? 'r.deleted_at DESC' : 'r.name ASC';
        $sql = "SELECT r.id, r.name, r.slug, r.method, r.image_path, r.views, r.updated_at, r.deleted_at,
                       (SELECT COUNT(*) FROM recipe_ingredients ri WHERE ri.recipe_id = r.id) AS ingredients,
                       (SELECT COUNT(*) FROM recipe_tags rt WHERE rt.recipe_id = r.id) AS tags
                FROM recipes r WHERE $cond ORDER BY $order";
        return $pdo->query($sql)->fetchAll();
    }

    /** Cuenta de recetas en la papelera. */
    public static function trashedCount(): int
    {
        return (int) Database::get()
            ->query('SELECT COUNT(*) FROM recipes WHERE deleted_at IS NOT NULL')
            ->fetchColumn();
    }

    /**
     * Receta para el formulario de edicion (incluye borradas).
     *
     * @return array<string,mixed>|null
     */
    public static function find(int $id): ?array
    {
        $pdo = Database::get();
        $stmt = $pdo->prepare('SELECT * FROM recipes WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch();
        if ($r === false) {
            return null;
        }

        $ing = $pdo->prepare(
            'SELECT raw_text FROM recipe_ingredients WHERE recipe_id = :id ORDER BY position ASC, id ASC'
        );
        $ing->execute([':id' => $id]);
        $r['ingredients_text'] = implode("\n", array_column($ing->fetchAll(), 'raw_text'));

        $tg = $pdo->prepare(
            'SELECT t.name FROM recipe_tags rt JOIN tags t ON t.id = rt.tag_id
             WHERE rt.recipe_id = :id ORDER BY t.name ASC'
        );
        $tg->execute([':id' => $id]);
        $r['tags_text'] = implode(', ', array_column($tg->fetchAll(), 'name'));

        $lk = $pdo->prepare(
            'SELECT label, url FROM recipe_links WHERE recipe_id = :id ORDER BY position ASC, id ASC'
        );
        $lk->execute([':id' => $id]);
        $r['links_text'] = implode("\n", array_map(
            static fn($l) => $l['label'] . ' | ' . $l['url'],
            $lk->fetchAll()
        ));

        return $r;
    }

    /**
     * Crea una receta. Devuelve el id nuevo.
     *
     * @param array<string,mixed> $d  datos ya validados
     */
    public static function create(array $d): int
    {
        $pdo = Database::get();
        $pdo->beginTransaction();
        try {
            $slug = self::uniqueSlug($d['name'], null);
            $stmt = $pdo->prepare(
                'INSERT INTO recipes
                    (name, slug, glassware, ice, method, method_other, method_detail,
                     volume, moment, family_id, garnish, description,
                     author_name, author_url, image_path, created_by)
                 VALUES
                    (:name, :slug, :glassware, :ice, :method, :method_other, :method_detail,
                     :volume, :moment, :family_id, :garnish, :description,
                     :author_name, :author_url, :image_path, :created_by)'
            );
            $stmt->execute(self::baseParams($d) + [
                ':slug'       => $slug,
                ':image_path' => $d['image_path'] ?? null,
                ':created_by' => $d['created_by'] ?? null,
            ]);
            $id = (int) $pdo->lastInsertId();

            self::syncIngredients($id, $d['ingredients_text'] ?? '');
            self::syncTags($id, $d['tags_text'] ?? '');
            self::syncLinks($id, $d['links_text'] ?? '');

            $pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza una receta existente. El slug se regenera desde el nombre.
     *
     * @param array<string,mixed> $d
     */
    public static function update(int $id, array $d): void
    {
        $pdo = Database::get();
        $pdo->beginTransaction();
        try {
            $slug = self::uniqueSlug($d['name'], $id);
            $sql = 'UPDATE recipes SET
                        name = :name, slug = :slug, glassware = :glassware, ice = :ice,
                        method = :method, method_other = :method_other, method_detail = :method_detail,
                        volume = :volume, moment = :moment, family_id = :family_id,
                        garnish = :garnish, description = :description,
                        author_name = :author_name, author_url = :author_url';
            $params = self::baseParams($d) + [':slug' => $slug, ':id' => $id];

            if (array_key_exists('image_path', $d)) {
                $sql .= ', image_path = :image_path';
                $params[':image_path'] = $d['image_path'];
            }
            $sql .= ' WHERE id = :id';
            $pdo->prepare($sql)->execute($params);

            self::syncIngredients($id, $d['ingredients_text'] ?? '');
            self::syncTags($id, $d['tags_text'] ?? '');
            self::syncLinks($id, $d['links_text'] ?? '');

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function softDelete(int $id): void
    {
        Database::get()
            ->prepare('UPDATE recipes SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL')
            ->execute([':id' => $id]);
    }

    public static function restore(int $id): void
    {
        Database::get()
            ->prepare('UPDATE recipes SET deleted_at = NULL WHERE id = :id')
            ->execute([':id' => $id]);
    }

    /* ------------------------------------------------------------------ */

    /**
     * Parametros comunes a INSERT y UPDATE (sin slug, image_path ni created_by).
     *
     * @param array<string,mixed> $d
     * @return array<string,mixed>
     */
    private static function baseParams(array $d): array
    {
        $method = in_array($d['method'] ?? '', self::METHODS, true) ? $d['method'] : 'otro';
        $volume = in_array($d['volume'] ?? '', self::VOLUMES, true) ? $d['volume'] : null;
        $moment = in_array($d['moment'] ?? '', self::MOMENTS, true) ? $d['moment'] : null;
        $familyId = (int) ($d['family_id'] ?? 0);
        return [
            ':name'          => trim((string) $d['name']),
            ':glassware'     => self::nn($d['glassware'] ?? null),
            ':ice'           => self::nn($d['ice'] ?? null),
            ':method'        => $method,
            ':method_other'  => $method === 'otro' ? self::nn($d['method_other'] ?? null) : null,
            ':method_detail' => self::nn($d['method_detail'] ?? null),
            ':volume'        => $volume,
            ':moment'        => $moment,
            ':family_id'     => $familyId > 0 ? $familyId : null,
            ':garnish'       => self::nn($d['garnish'] ?? null),
            ':description'    => self::nn($d['description'] ?? null),
            ':author_name'   => self::nn($d['author_name'] ?? null),
            ':author_url'    => safe_url($d['author_url'] ?? null),
        ];
    }

    /** '' -> null, trim en el resto. */
    private static function nn(?string $v): ?string
    {
        $v = $v === null ? null : trim($v);
        return ($v === null || $v === '') ? null : $v;
    }

    public static function syncIngredients(int $recipeId, string $text): void
    {
        $pdo = Database::get();
        $pdo->prepare('DELETE FROM recipe_ingredients WHERE recipe_id = :id')->execute([':id' => $recipeId]);

        $stmt = $pdo->prepare(
            'INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
             VALUES (:rid, :raw, :amount, :unit, :pos)'
        );
        $pos = 0;
        foreach (preg_split('/\r\n|\r|\n/', $text) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $p = parse_ingredient_line($line);
            $stmt->execute([
                ':rid'    => $recipeId,
                ':raw'    => mb_substr($p['raw'], 0, 255),
                ':amount' => $p['amount'],
                ':unit'   => $p['unit'],
                ':pos'    => ++$pos,
            ]);
        }
    }

    /** Crea los tags que no existan (por slug) y sincroniza la relacion. */
    public static function syncTags(int $recipeId, string $text): void
    {
        $pdo = Database::get();
        $pdo->prepare('DELETE FROM recipe_tags WHERE recipe_id = :id')->execute([':id' => $recipeId]);

        $seen = [];
        foreach (preg_split('/[,\n]/', $text) ?: [] as $raw) {
            $name = trim($raw);
            if ($name === '') {
                continue;
            }
            $slug = slugify($name);
            if ($slug === '' || isset($seen[$slug])) {
                continue;
            }
            $seen[$slug] = true;

            $find = $pdo->prepare('SELECT id FROM tags WHERE slug = :s LIMIT 1');
            $find->execute([':s' => $slug]);
            $tagId = $find->fetchColumn();
            if ($tagId === false) {
                $pdo->prepare('INSERT INTO tags (name, slug) VALUES (:n, :s)')
                    ->execute([':n' => mb_substr($name, 0, 60), ':s' => $slug]);
                $tagId = (int) $pdo->lastInsertId();
            }
            $pdo->prepare('INSERT IGNORE INTO recipe_tags (recipe_id, tag_id) VALUES (:r, :t)')
                ->execute([':r' => $recipeId, ':t' => $tagId]);
        }
    }

    /** Reemplaza los enlaces externos de la receta. */
    public static function syncLinks(int $recipeId, string $text): void
    {
        $pdo = Database::get();
        $pdo->prepare('DELETE FROM recipe_links WHERE recipe_id = :id')->execute([':id' => $recipeId]);

        $stmt = $pdo->prepare(
            'INSERT INTO recipe_links (recipe_id, label, url, position)
             VALUES (:rid, :label, :url, :pos)'
        );
        $pos = 0;
        foreach (parse_links_text($text) as $link) {
            $stmt->execute([
                ':rid'   => $recipeId,
                ':label' => $link['label'],
                ':url'   => $link['url'],
                ':pos'   => ++$pos,
            ]);
        }
    }

    /** Slug unico a partir del nombre; agrega -2, -3… si hace falta. */
    public static function uniqueSlug(string $name, ?int $ignoreId): string
    {
        $base = slugify($name);
        if ($base === '') {
            $base = 'receta';
        }
        $pdo = Database::get();
        $slug = $base;
        $i = 1;
        while (true) {
            $sql = 'SELECT COUNT(*) FROM recipes WHERE slug = :s'
                . ($ignoreId !== null ? ' AND id <> :id' : '');
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':s', $slug);
            if ($ignoreId !== null) {
                $stmt->bindValue(':id', $ignoreId, PDO::PARAM_INT);
            }
            $stmt->execute();
            if ((int) $stmt->fetchColumn() === 0) {
                return $slug;
            }
            $slug = $base . '-' . (++$i);
        }
    }

    /** Sugerencias de tags para autocompletar (por prefijo, o los mas usados). */
    public static function tagSuggestions(string $q, int $limit = 10): array
    {
        $pdo = Database::get();
        if (trim($q) === '') {
            $sql = 'SELECT t.name, t.slug, COUNT(rt.recipe_id) AS n
                    FROM tags t LEFT JOIN recipe_tags rt ON rt.tag_id = t.id
                    GROUP BY t.id, t.name, t.slug ORDER BY n DESC, t.name ASC LIMIT :lim';
            $stmt = $pdo->prepare($sql);
        } else {
            $sql = 'SELECT name, slug FROM tags
                    WHERE name LIKE :like OR slug LIKE :like2
                    ORDER BY name ASC LIMIT :lim';
            $stmt = $pdo->prepare($sql);
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($q)) . '%';
            $stmt->bindValue(':like', $like);
            $stmt->bindValue(':like2', $like);
        }
        $stmt->bindValue(':lim', max(1, min(25, $limit)), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
