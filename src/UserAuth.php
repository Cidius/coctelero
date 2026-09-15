<?php
declare(strict_types=1);

namespace App;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/helpers.php';

/**
 * Sesion de las cuentas publicas ("coctelero"). Login solo con Google
 * (sin contrasena, sin recuperacion que armar). Sesion y CSRF separados
 * de los del admin (Auth.php): cookie con otro nombre, no se pisan.
 */
final class UserAuth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $https = (($_SERVER['HTTPS'] ?? '') !== '' && ($_SERVER['HTTPS'] ?? '') !== 'off')
            || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';

        session_set_cookie_params([
            'lifetime' => 60 * 60 * 24 * 180, // 180 dias: no tiene sentido de seguridad hacerla corta (no hay password)
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => $https,
        ]);
        session_name('coctelero_user');
        session_start();
    }

    public static function check(): bool
    {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    /** @return array{id:int, name:string, email:string, avatar_url:?string, role:string}|null */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return [
            'id'         => (int) $_SESSION['user_id'],
            'name'       => (string) ($_SESSION['user_name'] ?? ''),
            'email'      => (string) ($_SESSION['user_email'] ?? ''),
            'avatar_url' => $_SESSION['user_avatar'] ?? null,
            'role'       => (string) ($_SESSION['user_role'] ?? 'coctelero'),
        ];
    }

    /**
     * "admin" acá solo controla que el menu del sitio muestre el link al
     * panel /admin; ESE panel sigue pidiendo su propio login por separado,
     * esto no reemplaza esa seguridad, es solo un atajo de navegacion.
     */
    public static function isAdmin(): bool
    {
        $u = self::user();
        return $u !== null && $u['role'] === 'admin';
    }

    /** Corta la pagina y manda a loguearse si no hay sesion. */
    public static function requireLogin(string $returnTo = '/'): void
    {
        if (!self::check()) {
            redirect('auth/google/login.php?return_to=' . urlencode(self::sanitizeReturnTo($returnTo)));
        }
    }

    /**
     * Crea/actualiza la cuenta a partir del perfil ya validado por Google
     * y abre sesion.
     *
     * @param array{sub:string,email:string,name:string,picture:?string} $profile
     */
    public static function loginWithGoogle(array $profile): void
    {
        self::startSession();
        $pdo = Database::get();

        $stmt = $pdo->prepare('SELECT id FROM users WHERE google_sub = :sub LIMIT 1');
        $stmt->execute([':sub' => $profile['sub']]);
        $id = $stmt->fetchColumn();

        if ($id === false) {
            $pdo->prepare(
                'INSERT INTO users (google_sub, email, name, avatar_url) VALUES (:sub, :email, :name, :avatar)'
            )->execute([
                ':sub'    => $profile['sub'],
                ':email'  => $profile['email'],
                ':name'   => $profile['name'],
                ':avatar' => $profile['picture'],
            ]);
            $id = (int) $pdo->lastInsertId();
        } else {
            $pdo->prepare(
                'UPDATE users SET email = :email, name = :name, avatar_url = :avatar, last_login_at = NOW()
                 WHERE id = :id'
            )->execute([
                ':email'  => $profile['email'],
                ':name'   => $profile['name'],
                ':avatar' => $profile['picture'],
                ':id'     => $id,
            ]);
        }

        $roleStmt = $pdo->prepare('SELECT role FROM users WHERE id = :id');
        $roleStmt->execute([':id' => $id]);
        $role = (string) ($roleStmt->fetchColumn() ?: 'coctelero');

        session_regenerate_id(true);
        $_SESSION['user_id']     = (int) $id;
        $_SESSION['user_name']   = $profile['name'];
        $_SESSION['user_email']  = $profile['email'];
        $_SESSION['user_avatar'] = $profile['picture'];
        $_SESSION['user_role']   = $role;
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

    /** Solo rutas propias relativas; nunca un destino externo (open redirect). */
    public static function sanitizeReturnTo(string $path): string
    {
        if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//')) {
            return '/';
        }
        return $path;
    }

    /** Menu hamburguesa del header publico (Inicio, favoritos, contacto, admin si corresponde, login/logout). */
    public static function headerHtml(): string
    {
        $return = urlencode(self::sanitizeReturnTo($_SERVER['REQUEST_URI'] ?? '/'));
        $u = self::user();

        $links = [
            '<a href="' . e(url('/')) . '">Inicio</a>',
            '<a href="' . e(url('favoritos.php')) . '">Mis favoritos</a>',
            '<a href="' . e(url('contacto.php')) . '">Contacto</a>',
        ];
        if (self::isAdmin()) {
            $links[] = '<a href="' . e(url('admin/dashboard.php')) . '">Panel admin</a>';
        }

        $account = '';
        if ($u !== null) {
            $avatar = !empty($u['avatar_url'])
                ? '<img class="user-avatar" src="' . e($u['avatar_url']) . '" alt="">'
                : '';
            $account = '<div class="nav-user">' . $avatar . '<span>' . e($u['name']) . '</span></div>'
                . '<a href="' . e(url('auth/logout.php?return_to=' . $return)) . '">Salir</a>';
        } else {
            $account = '<a href="' . e(url('auth/google/login.php?return_to=' . $return)) . '">'
                . 'Iniciar sesión con Google</a>';
        }

        return '<div class="site-nav">'
            . '<button type="button" id="nav-toggle" class="nav-toggle" aria-expanded="false" '
            . 'aria-controls="nav-panel" aria-label="Menú">☰</button>'
            . '<nav id="nav-panel" class="nav-panel" hidden>'
            . implode('', $links)
            . '<hr>'
            . $account
            . '</nav>'
            . '</div>';
    }

    /* ---------------- CSRF (independiente del de Auth/admin) ---------------- */

    public static function csrfToken(): string
    {
        self::startSession();
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    public static function csrfField(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::csrfToken()) . '">';
    }

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
