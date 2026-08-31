<?php
declare(strict_types=1);

namespace App;

use PDO;

require_once __DIR__ . '/Database.php';

/**
 * Consultas de recetas para el front publico (solo lectura).
 * Todas filtran deleted_at IS NULL.
 */
final class Recipe
{
    private const PER_PAGE_DEFAULT = 24;
    private const PER_PAGE_MAX = 60;

    public const METHODS = [
        'integrado'          => 'Integrado',
        'refrescado_directo' => 'Refrescado / Directo',
        'batido'             => 'Batido',
        'machacado'          => 'Machacado',
        'frozen'             => 'Frozen',
        'otro'               => 'Otro',
    ];

    /**
     * Busqueda + filtros combinables.
     *
     * @param array{q?:string, tags?:list<string>, method?:string, page?:int, per_page?:int} $p
     * @return array{data:list<array<string,mixed>>, meta:array<string,int>}
     */
    public static function search(array $p): array
    {
        $pdo = Database::get();

        $q       = trim((string) ($p['q'] ?? ''));
        $tags    = array_values(array_unique($p['tags'] ?? []));
        $method  = (string) ($p['method'] ?? '');
        $page    = max(1, (int) ($p['page'] ?? 1));
        $perPage = (int) ($p['per_page'] ?? self::PER_PAGE_DEFAULT);
        $perPage = max(1, min(self::PER_PAGE_MAX, $perPage));
        $offset  = ($page - 1) * $perPage;

        $where  = ['r.deleted_at IS NULL'];
        $params = [];

        if ($q !== '') {
            // FULLTEXT sobre name+description, con fallback LIKE a nombre e ingredientes.
            $where[] = '('
                . 'MATCH(r.name, r.description) AGAINST (:ft IN BOOLEAN MODE) '
                . 'OR r.name LIKE :like '
                . 'OR EXISTS (SELECT 1 FROM recipe_ingredients ri '
                . '           WHERE ri.recipe_id = r.id AND ri.raw_text LIKE :like)'
                . ')';
            $params[':ft']   = self::booleanQuery($q);
            $params[':like'] = '%' . self::escapeLike($q) . '%';
        }

        if ($method !== '' && isset(self::METHODS[$method])) {
            $where[] = 'r.method = :method';
            $params[':method'] = $method;
        }

        if ($tags !== []) {
            $in = [];
            foreach ($tags as $i => $slug) {
                $key = ':tag' . $i;
                $in[] = $key;
                $params[$key] = $slug;
            }
            $where[] = 'r.id IN ('
                . ' SELECT rt.recipe_id FROM recipe_tags rt'
                . ' JOIN tags t ON t.id = rt.tag_id'
                . ' WHERE t.slug IN (' . implode(', ', $in) . ')'
                . ' GROUP BY rt.recipe_id'
                . ' HAVING COUNT(DISTINCT t.slug) = :tagcount'
                . ')';
            $params[':tagcount'] = count($tags);
        }

        $whereSql = implode(' AND ', $where);

        $total = (int) self::bind(
            $pdo->prepare("SELECT COUNT(*) FROM recipes r WHERE $whereSql"),
            $params
        )->fetchColumn();

        $sql = "SELECT r.id, r.name, r.slug, r.glassware, r.ice, r.method,
                       r.method_other, r.method_detail, r.garnish, r.image_path
                FROM recipes r
                WHERE $whereSql
                ORDER BY r.name ASC
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        self::attachTags($pdo, $rows);

        return [
            'data' => $rows,
            'meta' => [
                'page'     => $page,
                'per_page' => $perPage,
                'total'    => $total,
                'pages'    => (int) ceil($total / $perPage),
            ],
        ];
    }

    /**
     * Receta completa por slug (con ingredientes y tags). null si no existe.
     *
     * @return array<string,mixed>|null
     */
    public static function findBySlug(string $slug): ?array
    {
        $pdo = Database::get();
        $stmt = $pdo->prepare(
            'SELECT * FROM recipes WHERE slug = :slug AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute([':slug' => $slug]);
        $recipe = $stmt->fetch();
        if ($recipe === false) {
            return null;
        }

        $ing = $pdo->prepare(
            'SELECT raw_text, amount, unit, position
             FROM recipe_ingredients WHERE recipe_id = :id ORDER BY position ASC, id ASC'
        );
        $ing->execute([':id' => $recipe['id']]);
        $recipe['ingredients'] = $ing->fetchAll();

        $tg = $pdo->prepare(
            'SELECT t.name, t.slug FROM recipe_tags rt
             JOIN tags t ON t.id = rt.tag_id
             WHERE rt.recipe_id = :id ORDER BY t.name ASC'
        );
        $tg->execute([':id' => $recipe['id']]);
        $recipe['tags'] = $tg->fetchAll();

        return $recipe;
    }

    /**
     * Tags con al menos una receta activa, con su conteo. Para el panel de filtros.
     *
     * @return list<array{name:string, slug:string, count:int}>
     */
    public static function tagsWithCounts(): array
    {
        $pdo = Database::get();
        $sql = 'SELECT t.name, t.slug, COUNT(*) AS count
                FROM tags t
                JOIN recipe_tags rt ON rt.tag_id = t.id
                JOIN recipes r ON r.id = rt.recipe_id AND r.deleted_at IS NULL
                GROUP BY t.id, t.name, t.slug
                ORDER BY count DESC, t.name ASC';
        return array_map(
            static fn($r) => ['name' => $r['name'], 'slug' => $r['slug'], 'count' => (int) $r['count']],
            $pdo->query($sql)->fetchAll()
        );
    }

    /**
     * Metodos presentes en recetas activas, con conteo y etiqueta.
     *
     * @return list<array{value:string, label:string, count:int}>
     */
    public static function methodsWithCounts(): array
    {
        $pdo = Database::get();
        $rows = $pdo->query(
            'SELECT method, COUNT(*) AS count FROM recipes
             WHERE deleted_at IS NULL GROUP BY method'
        )->fetchAll();
        $counts = [];
        foreach ($rows as $r) {
            $counts[$r['method']] = (int) $r['count'];
        }
        $out = [];
        foreach (self::METHODS as $value => $label) {
            if (!empty($counts[$value])) {
                $out[] = ['value' => $value, 'label' => $label, 'count' => $counts[$value]];
            }
        }
        return $out;
    }

    /* ------------------------------------------------------------------ */

    /** @param list<array<string,mixed>> $rows */
    private static function attachTags(PDO $pdo, array &$rows): void
    {
        if ($rows === []) {
            return;
        }
        $ids = array_column($rows, 'id');
        $ph = implode(', ', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare(
            "SELECT rt.recipe_id, t.name, t.slug
             FROM recipe_tags rt JOIN tags t ON t.id = rt.tag_id
             WHERE rt.recipe_id IN ($ph)
             ORDER BY t.name ASC"
        );
        $stmt->execute($ids);

        $byRecipe = [];
        foreach ($stmt->fetchAll() as $r) {
            $byRecipe[(int) $r['recipe_id']][] = ['name' => $r['name'], 'slug' => $r['slug']];
        }
        foreach ($rows as &$row) {
            $row['tags'] = $byRecipe[(int) $row['id']] ?? [];
        }
        unset($row);
    }

    private static function bind(\PDOStatement $stmt, array $params): \PDOStatement
    {
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt;
    }

    private static function escapeLike(string $s): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $s);
    }

    /** Convierte "gin limon" en "+gin* +limon*" para BOOLEAN MODE. */
    private static function booleanQuery(string $q): string
    {
        $terms = preg_split('/\s+/', trim($q)) ?: [];
        $out = [];
        foreach ($terms as $t) {
            $t = preg_replace('/[+\-><()~*"@]+/', '', $t) ?? '';
            if (mb_strlen($t) >= 2) {
                $out[] = '+' . $t . '*';
            }
        }
        return implode(' ', $out);
    }
}
