<?php
declare(strict_types=1);

namespace App;

require_once __DIR__ . '/helpers.php';

/**
 * Google Sign-In (OAuth 2.0 / OpenID Connect), sin dependencias externas.
 *
 * La verificacion del id_token se delega al endpoint tokeninfo de Google
 * (firma, expiracion y formato validados por Google) en vez de implementar
 * la verificacion JWT/JWK nosotros — mas simple y suficiente para el
 * volumen de este sitio.
 */
final class GoogleAuth
{
    private const AUTH_ENDPOINT      = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_ENDPOINT     = 'https://oauth2.googleapis.com/token';
    private const TOKENINFO_ENDPOINT = 'https://oauth2.googleapis.com/tokeninfo';

    public static function authorizeUrl(string $state): string
    {
        $cfg = self::cfg();
        $params = [
            'client_id'     => $cfg['client_id'],
            'redirect_uri'  => $cfg['redirect_uri'],
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'prompt'        => 'select_account',
        ];
        return self::AUTH_ENDPOINT . '?' . http_build_query($params);
    }

    /**
     * Intercambia el code por tokens y valida el id_token contra Google.
     *
     * @return array{sub:string,email:string,name:string,picture:?string}
     */
    public static function exchangeCodeForProfile(string $code): array
    {
        $cfg = self::cfg();

        $tokenResp = self::request(self::TOKEN_ENDPOINT, 'POST', [
            'code'          => $code,
            'client_id'     => $cfg['client_id'],
            'client_secret' => $cfg['client_secret'],
            'redirect_uri'  => $cfg['redirect_uri'],
            'grant_type'    => 'authorization_code',
        ]);

        $idToken = $tokenResp['id_token'] ?? null;
        if (!is_string($idToken) || $idToken === '') {
            throw new \RuntimeException('Google no devolvió id_token.');
        }

        $info = self::request(self::TOKENINFO_ENDPOINT . '?id_token=' . urlencode($idToken), 'GET', null);

        if (($info['aud'] ?? null) !== $cfg['client_id']) {
            throw new \RuntimeException('Token de Google con audiencia inválida.');
        }
        $iss = (string) ($info['iss'] ?? '');
        if ($iss !== 'https://accounts.google.com' && $iss !== 'accounts.google.com') {
            throw new \RuntimeException('Token de Google con emisor inválido.');
        }
        $verified = $info['email_verified'] ?? false;
        if ($verified !== true && $verified !== 'true') {
            throw new \RuntimeException('El email de Google no está verificado.');
        }

        $sub = (string) ($info['sub'] ?? '');
        $email = (string) ($info['email'] ?? '');
        if ($sub === '' || $email === '') {
            throw new \RuntimeException('Perfil de Google incompleto.');
        }

        return [
            'sub'     => $sub,
            'email'   => $email,
            'name'    => (string) ($info['name'] ?? explode('@', $email)[0]),
            'picture' => isset($info['picture']) ? (string) $info['picture'] : null,
        ];
    }

    private static function cfg(): array
    {
        $g = config()['google'] ?? [];
        if (empty($g['client_id']) || empty($g['client_secret']) || empty($g['redirect_uri'])) {
            throw new \RuntimeException(
                'Falta configurar config["google"] (client_id / client_secret / redirect_uri).'
            );
        }
        return $g;
    }

    private static function request(string $url, string $method, ?array $fields): array
    {
        if (!function_exists('curl_init')) {
            throw new \RuntimeException('La extension curl de PHP no esta disponible en el servidor.');
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CUSTOMREQUEST  => $method,
        ]);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields ?? []));
        }
        $raw = curl_exec($ch);
        $err = curl_error($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false) {
            throw new \RuntimeException('Error de red hacia Google: ' . $err);
        }
        $data = json_decode((string) $raw, true);
        if (!is_array($data)) {
            throw new \RuntimeException('Respuesta inválida de Google.');
        }
        if ($code >= 400) {
            $msg = $data['error_description'] ?? $data['error'] ?? 'desconocido';
            throw new \RuntimeException("Google devolvió error $code: $msg");
        }
        return $data;
    }
}
