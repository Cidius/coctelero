<?php
declare(strict_types=1);

namespace App;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Conexion PDO unica (singleton) a MySQL/MariaDB.
 *
 * Uso:
 *   $pdo = \App\Database::get();
 *   $stmt = $pdo->prepare('SELECT * FROM recipes WHERE slug = ?');
 *   $stmt->execute([$slug]);
 */
final class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function get(): PDO
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        $config = self::loadConfig();
        $db = $config['db'];

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $db['host'],
            $db['port'] ?? 3306,
            $db['name'],
            $db['charset'] ?? 'utf8mb4'
        );

        try {
            self::$instance = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // No filtrar credenciales ni el DSN al cliente.
            if (($config['env'] ?? 'prod') === 'dev') {
                throw $e;
            }
            error_log('DB connection failed: ' . $e->getMessage());
            http_response_code(500);
            exit('Error de conexion a la base de datos.');
        }

        return self::$instance;
    }

    /**
     * Carga config.php desde la raiz del proyecto (un nivel arriba de /src).
     */
    public static function loadConfig(): array
    {
        static $config = null;
        if ($config !== null) {
            return $config;
        }

        $path = dirname(__DIR__) . '/config.php';
        if (!is_file($path)) {
            throw new RuntimeException(
                'Falta config.php. Copiar config.php.example a config.php y completar los datos.'
            );
        }

        $config = require $path;
        if (!is_array($config)) {
            throw new RuntimeException('config.php debe retornar un array.');
        }

        return $config;
    }
}
