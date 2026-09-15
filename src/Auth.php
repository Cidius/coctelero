<?php
declare(strict_types=1);

namespace App;

use PDO;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/helpers.php';

/**
 * Login del admin: sesion, CSRF y limite de intentos.
 * Un solo usuario (sin roles) -> no hay concepto de permisos.
 */
final class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $https = (($_SERVER['HTTPS'] ?? '') !== '' && ($_SERVER['HTTPS'] ?? '') !== 'off')
            || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => $https,
        ]);
        session_name('coctelero_admin');
        session_start();
    }

    public static function check(): bool
    {
        self::startSession();
        return isset($_SESSION['admin_id']);
    }

    public static function requireLogin(): void
    {
        if (self::check()) {
            return;
        }
        if (self::bridgeFromGoogleUser()) {
            return;
        }
        redirect('admin/login.php');
    }

    /**
     * Si no hay sesion de admin pero el navegador trae la cookie firmada
     * que UserAuth deja cuando la cuenta de Google logueada es admin, abre
     * sesion de admin sin pedir usuario/contrasena. Se une a la unica fila
     * de admin_users que existe (esta app es de un solo admin).
     *
     * Verifica la firma (HMAC) de la cookie en vez de abrir la sesion de
     * UserAuth: PHP solo permite una sesion nativa activa por request, y
     * cerrar/reabrir dos sesiones distintas en el mismo script resulto
     * poco confiable en la practica.
     */
    public static function bridgeFromGoogleUser(): bool
    {
        $cookie = (string) ($_COOKIE['coctelero_admin_hint'] ?? '');
        if ($cookie === '' || !str_contains($cookie, '.')) {
            return false;
        }
        [$payload, $sig] = explode('.', $cookie, 2);
        if (!hash_equals(hash_hmac('sha256', $payload, admin_hint_secret()), $sig)) {
            return false;
        }
        $exp = (int) (explode(':', $payload)[1] ?? 0);
        if ($exp < time()) {
            return false;
        }

        $row = Database::get()->query('SELECT id, username FROM admin_users LIMIT 1')->fetch();
        if ($row === false) {
            return false;
        }

        self::startSession();
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $row['id'];
        $_SESSION['admin_username'] = $row['username'];
        return true;
    }

    /** @return array{id:int, username:string}|null */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return [
            'id'       => (int) $_SESSION['admin_id'],
            'username' => (string) ($_SESSION['admin_username'] ?? ''),
        ];
    }

    /**
     * Intenta autenticar. Devuelve null si OK, o un mensaje de error.
     */
    public static function attempt(string $username, string $password, string $ip): ?string
    {
        self::startSession();
        $cfg = config();
        $max = (int) ($cfg['login_max_attempts'] ?? 5);
        $lock = (int) ($cfg['login_lockout_minutes'] ?? 15);
        $pdo = Database::get();

        // Intentos fallidos recientes de esta IP. $lock es un int de config,
        // se interpola directo (INTERVAL ? no es portable con prepares).
        $lock = max(1, min(1440, $lock));
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM login_attempts
             WHERE ip = :ip AND success = 0
               AND attempted_at > (NOW() - INTERVAL $lock MINUTE)"
        );
        $stmt->bindValue(':ip', $ip);
        $stmt->execute();
        if ((int) $stmt->fetchColumn() >= $max) {
            return "Demasiados intentos fallidos. Probá de nuevo en $lock minutos.";
        }

        $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = :u LIMIT 1');
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch();

        $ok = $row !== false && password_verify($password, $row['password_hash']);

        $pdo->prepare('INSERT INTO login_attempts (ip, username, success) VALUES (:ip, :u, :s)')
            ->execute([':ip' => $ip, ':u' => $username, ':s' => $ok ? 1 : 0]);

        if (!$ok) {
            return 'Usuario o contraseña incorrectos.';
        }

        // Rehash si el algoritmo quedo desactualizado.
        if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) {
            $pdo->prepare('UPDATE admin_users SET password_hash = :h WHERE id = :id')
                ->execute([':h' => password_hash($password, PASSWORD_DEFAULT), ':id' => $row['id']]);
        }

        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $row['id'];
        $_SESSION['admin_username'] = $row['username'];
        return null;
    }

    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    /* ---------------- CSRF ---------------- */

    public static function csrfToken(): string
    {
        self::startSession();
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    /** Campo hidden listo para pegar en un form. */
    public static function csrfField(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::csrfToken()) . '">';
    }

    /** Valida el token del POST. Corta con 400 si no coincide. */
    public static function requireCsrf(): void
    {
        self::startSession();
        $sent = (string) ($_POST['_csrf'] ?? '');
        if ($sent === '' || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $sent)) {
            http_response_code(400);
            exit('Token de seguridad inválido. Volvé atrás y reintentá.');
        }
    }
}
