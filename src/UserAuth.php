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

        self::setAdminHint($role === 'admin' ? (int) $id : null);
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
        self::setAdminHint(null); // revoca tambien el atajo al panel admin
    }

    /**
     * Revoca el atajo Google -> panel admin sin tocar la sesion de Google
     * en si. La usa admin/logout.php: sin esto, "Salir" del panel no
     * serviria de nada mientras la cuenta de Google siga logueada (la
     * proxima pagina del admin te volvería a meter solo por el bridge).
     */
    public static function clearAdminHint(): void
    {
        self::setAdminHint(null);
    }

    /**
     * Cookie firmada (HMAC) que le permite a Auth::bridgeFromGoogleUser()
     * reconocer sin abrir esta sesion que la cuenta logueada es admin.
     * No guarda datos de sesion, solo "esta cuenta X es admin hasta tal
     * fecha", verificable con la firma. $userId = null la borra.
     */
    private static function setAdminHint(?int $userId): void
    {
        $https = (($_SERVER['HTTPS'] ?? '') !== '' && ($_SERVER['HTTPS'] ?? '') !== 'off')
            || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';

        if ($userId === null) {
            setcookie('coctelero_admin_hint', '', time() - 3600, '/', '', $https, true);
            return;
        }

        $exp = time() + 60 * 60 * 24 * 180;
        $payload = $userId . ':' . $exp;
        $sig = hash_hmac('sha256', $payload, admin_hint_secret());

        setcookie('coctelero_admin_hint', $payload . '.' . $sig, [
            'expires'  => $exp,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => $https,
        ]);
    }

    /** Solo rutas propias relativas; nunca un destino externo (open redirect). */
    public static function sanitizeReturnTo(string $path): string
    {
        if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//')) {
            return '/';
        }
        return $path;
    }

    /**
     * Menu hamburguesa del header publico: boton + sidebar que se desliza
     * desde el costado, con la cuenta (foto) arriba, los links en el medio
     * y salir/entrar abajo.
     */
    public static function headerHtml(): string
    {
        $return = urlencode(self::sanitizeReturnTo($_SERVER['REQUEST_URI'] ?? '/'));
        $u = self::user();

        // Boton de instalar PWA: oculto por defecto, menu.js lo muestra solo
        // cuando el navegador ofrece beforeinstallprompt (o, en iOS, con el
        // instructivo manual) y nunca si ya se esta usando como app instalada.
        $installBtn = '<button type="button" id="pwa-install-btn" class="btn nav-install" hidden>'
            . 'Instalar app</button>';

        if ($u === null) {
            // Deslogueado: solo Inicio + el CTA de login bien arriba y visible,
            // nada de favoritos/contacto/admin hasta que inicie sesion.
            $links = ['<a href="' . e(url('/')) . '">Inicio</a>'];
            $googleIcon = '<svg class="google-icon" viewBox="0 0 18 18" aria-hidden="true">'
                . '<path fill="#4285F4" d="M17.64 9.2c0-.64-.06-1.25-.16-1.84H9v3.48h4.84a4.14 4.14 0 0 1-1.8 2.72v2.26h2.9c1.7-1.57 2.7-3.88 2.7-6.62Z"/>'
                . '<path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.9-2.26c-.8.54-1.84.86-3.06.86-2.35 0-4.34-1.59-5.05-3.72H.95v2.33A9 9 0 0 0 9 18Z"/>'
                . '<path fill="#FBBC05" d="M3.95 10.7A5.4 5.4 0 0 1 3.67 9c0-.59.1-1.16.28-1.7V4.97H.95A9 9 0 0 0 0 9c0 1.45.35 2.83.95 4.03l3-2.33Z"/>'
                . '<path fill="#EA4335" d="M9 3.58c1.32 0 2.51.46 3.44 1.35l2.58-2.58C13.46.9 11.43 0 9 0A9 9 0 0 0 .95 4.97l3 2.33C4.66 5.17 6.65 3.58 9 3.58Z"/>'
                . '</svg>';
            $userBlock = '<div class="nav-login-cta">'
                . '<a class="btn primary nav-login" href="'
                . e(url('auth/google/login.php?return_to=' . $return)) . '">'
                . $googleIcon . 'Iniciar sesión con Google</a></div>';
            $footer = $installBtn;
        } else {
            $links = [
                '<a href="' . e(url('/')) . '">Inicio</a>',
                '<a href="' . e(url('favoritos.php')) . '">Mis favoritos</a>',
                '<a href="' . e(url('contacto.php')) . '">Contacto</a>',
            ];
            if (self::isAdmin()) {
                $links[] = '<a href="' . e(url('admin/dashboard.php')) . '">Panel admin</a>';
            }

            $initial = mb_strtoupper(mb_substr($u['name'] !== '' ? $u['name'] : '?', 0, 1));
            $avatar = !empty($u['avatar_url'])
                ? '<img class="user-avatar" src="' . e($u['avatar_url']) . '" alt="">'
                : '<span class="user-avatar user-avatar-fallback">' . e($initial) . '</span>';
            $userBlock = '<div class="nav-user">' . $avatar
                . '<div class="nav-user-info"><strong>' . e($u['name']) . '</strong>'
                . '<span class="muted small">' . e($u['email']) . '</span></div>'
                . '</div>';
            $footer = $installBtn . '<a href="' . e(url('auth/logout.php?return_to=' . $return)) . '">Salir</a>';
        }

        return '<div class="site-nav">'
            . '<button type="button" id="nav-toggle" class="nav-toggle" aria-expanded="false" '
            . 'aria-controls="nav-panel" aria-label="Abrir menú">☰</button>'
            . '<div id="nav-backdrop" class="nav-backdrop" hidden></div>'
            . '<nav id="nav-panel" class="nav-panel" aria-label="Menú">'
            . '<button type="button" id="nav-close" class="nav-close" aria-label="Cerrar menú">✕</button>'
            . $userBlock
            . '<div class="nav-links">' . implode('', $links) . '</div>'
            . '<div class="nav-footer">' . $footer . '</div>'
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
