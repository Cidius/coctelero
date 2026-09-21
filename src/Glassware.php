<?php
declare(strict_types=1);

namespace App;

require_once __DIR__ . '/Database.php';

/**
 * Catalogo de cristaleria (antes texto libre en recipes.glassware).
 * ABM desde /admin/cristaleria.php; el form de recetas elige de esta lista.
 */
final class Glassware
{
    /** Para el <select> del form de recetas. @return list<array{id:int, name:string}> */
    public static function all(): array
    {
        return Database::get()
            ->query('SELECT id, name FROM glassware ORDER BY position ASC, name ASC')
            ->fetchAll();
    }

    /** Con cantidad de recetas que la usan, para /admin/cristaleria.php. */
    public static function allWithUsage(): array
    {
        return Database::get()->query(
            'SELECT g.id, g.name, g.position, COUNT(r.id) AS uses
             FROM glassware g
             LEFT JOIN recipes r ON r.glassware_id = g.id
             GROUP BY g.id, g.name, g.position
             ORDER BY g.position ASC, g.name ASC'
        )->fetchAll();
    }

    /** @throws \InvalidArgumentException nombre vacio o duplicado */
    public static function create(string $name): void
    {
        $name = trim($name);
        if ($name === '') {
            throw new \InvalidArgumentException('El nombre no puede estar vacío.');
        }
        $pdo = Database::get();
        $pos = (int) $pdo->query('SELECT COALESCE(MAX(position), 0) FROM glassware')->fetchColumn();
        try {
            $pdo->prepare('INSERT INTO glassware (name, position) VALUES (:n, :p)')
                ->execute([':n' => $name, ':p' => $pos + 1]);
        } catch (\PDOException $e) {
            if ((int) $e->getCode() === 23000) {
                throw new \InvalidArgumentException('Ya existe una cristalería con ese nombre.');
            }
            throw $e;
        }
    }

    /** @throws \InvalidArgumentException nombre vacio o duplicado */
    public static function rename(int $id, string $name): void
    {
        $name = trim($name);
        if ($name === '') {
            throw new \InvalidArgumentException('El nombre no puede estar vacío.');
        }
        try {
            Database::get()
                ->prepare('UPDATE glassware SET name = :n WHERE id = :id')
                ->execute([':n' => $name, ':id' => $id]);
        } catch (\PDOException $e) {
            if ((int) $e->getCode() === 23000) {
                throw new \InvalidArgumentException('Ya existe una cristalería con ese nombre.');
            }
            throw $e;
        }
    }

    /** No borra si hay recetas usandola. Devuelve false en ese caso. */
    public static function delete(int $id): bool
    {
        $pdo = Database::get();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM recipes WHERE glassware_id = :id');
        $stmt->execute([':id' => $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            return false;
        }
        $pdo->prepare('DELETE FROM glassware WHERE id = :id')->execute([':id' => $id]);
        return true;
    }
}
