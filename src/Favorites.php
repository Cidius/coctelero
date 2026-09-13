<?php
declare(strict_types=1);

namespace App;

require_once __DIR__ . '/Database.php';

/**
 * Like + favorito son la misma accion: un tap por usuario/receta.
 * El COUNT de la tabla es el "like" publico; las filas de un usuario
 * son su lista de favoritos.
 */
final class Favorites
{
    public static function isFavorited(int $userId, int $recipeId): bool
    {
        $stmt = Database::get()->prepare(
            'SELECT 1 FROM recipe_favorites WHERE user_id = :u AND recipe_id = :r LIMIT 1'
        );
        $stmt->execute([':u' => $userId, ':r' => $recipeId]);
        return $stmt->fetchColumn() !== false;
    }

    public static function count(int $recipeId): int
    {
        $stmt = Database::get()->prepare('SELECT COUNT(*) FROM recipe_favorites WHERE recipe_id = :r');
        $stmt->execute([':r' => $recipeId]);
        return (int) $stmt->fetchColumn();
    }

    /** @return array{0: bool, 1: int} [ahora favorita?, conteo total] */
    public static function toggle(int $userId, int $recipeId): array
    {
        $pdo = Database::get();
        if (self::isFavorited($userId, $recipeId)) {
            $pdo->prepare('DELETE FROM recipe_favorites WHERE user_id = :u AND recipe_id = :r')
                ->execute([':u' => $userId, ':r' => $recipeId]);
            $now = false;
        } else {
            $pdo->prepare('INSERT IGNORE INTO recipe_favorites (user_id, recipe_id) VALUES (:u, :r)')
                ->execute([':u' => $userId, ':r' => $recipeId]);
            $now = true;
        }
        return [$now, self::count($recipeId)];
    }

    /**
     * Recetas favoritas de un usuario (activas), mas nuevas primero.
     * Forma compatible con render_recipe_card().
     *
     * @return list<array<string,mixed>>
     */
    public static function forUser(int $userId): array
    {
        $pdo = Database::get();
        $stmt = $pdo->prepare(
            'SELECT r.id, r.name, r.slug, r.glassware, r.image_path, f.name AS family
             FROM recipe_favorites rf
             JOIN recipes r ON r.id = rf.recipe_id AND r.deleted_at IS NULL
             LEFT JOIN families f ON f.id = r.family_id
             WHERE rf.user_id = :u
             ORDER BY rf.created_at DESC'
        );
        $stmt->execute([':u' => $userId]);
        $rows = $stmt->fetchAll();
        if ($rows === []) {
            return $rows;
        }

        $ids = array_column($rows, 'id');
        $ph = implode(', ', array_fill(0, count($ids), '?'));
        $tagStmt = $pdo->prepare(
            "SELECT rt.recipe_id, t.name, t.slug FROM recipe_tags rt
             JOIN tags t ON t.id = rt.tag_id
             WHERE rt.recipe_id IN ($ph) ORDER BY t.name ASC"
        );
        $tagStmt->execute($ids);
        $byRecipe = [];
        foreach ($tagStmt->fetchAll() as $t) {
            $byRecipe[(int) $t['recipe_id']][] = ['name' => $t['name'], 'slug' => $t['slug']];
        }
        foreach ($rows as &$row) {
            $row['tags'] = $byRecipe[(int) $row['id']] ?? [];
        }
        unset($row);
        return $rows;
    }
}
