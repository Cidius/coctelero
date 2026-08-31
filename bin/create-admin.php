<?php
declare(strict_types=1);

/**
 * Crea (o actualiza la contrasena de) el usuario admin.
 * No hay registro por web: el unico admin se da de alta aca.
 *
 *   php bin/create-admin.php <usuario>
 *
 * Pide la contrasena por consola (no queda en el historial).
 * Requiere config.php y el schema ya cargado.
 */

require __DIR__ . '/../src/Database.php';

use App\Database;

if (PHP_SAPI !== 'cli') {
    exit("Solo por linea de comandos.\n");
}

$username = $argv[1] ?? '';
if ($username === '' || !preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $username)) {
    fwrite(STDERR, "Uso: php bin/create-admin.php <usuario> [contrasena]\n");
    fwrite(STDERR, "  usuario: 3-50 caracteres (letras, numeros, . _ -)\n");
    fwrite(STDERR, "  contrasena: opcional; si falta se pide por consola.\n");
    fwrite(STDERR, "  Tambien lee ADMIN_PASSWORD del entorno.\n");
    exit(1);
}

// Leer contrasena sin eco cuando se puede.
function prompt_hidden(string $label): string
{
    fwrite(STDOUT, $label);
    if (stripos(PHP_OS, 'WIN') === 0) {
        $p = rtrim((string) shell_exec(
            'powershell -NoProfile -Command "$p = Read-Host -AsSecureString; ' .
            '[Runtime.InteropServices.Marshal]::PtrToStringAuto(' .
            '[Runtime.InteropServices.Marshal]::SecureStringToBSTR($p))"'
        ), "\r\n");
        fwrite(STDOUT, "\n");
        return $p;
    }
    shell_exec('stty -echo');
    $p = rtrim((string) fgets(STDIN), "\n");
    shell_exec('stty echo');
    fwrite(STDOUT, "\n");
    return $p;
}

// Modo no interactivo (util para el Cron de Hostinger, sin SSH):
//   php bin/create-admin.php <usuario> <contrasena>
//   o  ADMIN_PASSWORD='...' php bin/create-admin.php <usuario>
$envPass = getenv('ADMIN_PASSWORD');
$argPass = $argv[2] ?? '';

if ($argPass !== '' || $envPass !== false) {
    $pass = $argPass !== '' ? $argPass : (string) $envPass;
} else {
    $pass = prompt_hidden("Contrasena para '$username': ");
    $pass2 = prompt_hidden('Repetir: ');
    if ($pass !== $pass2) {
        fwrite(STDERR, "No coinciden.\n");
        exit(1);
    }
}
if (strlen($pass) < 10) {
    fwrite(STDERR, "Minimo 10 caracteres.\n");
    exit(1);
}

$hash = password_hash($pass, PASSWORD_DEFAULT);
$pdo = Database::get();

$stmt = $pdo->prepare('SELECT id FROM admin_users WHERE username = ?');
$stmt->execute([$username]);
$id = $stmt->fetchColumn();

if ($id) {
    $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')
        ->execute([$hash, $id]);
    echo "Contrasena actualizada para '$username' (id $id).\n";
} else {
    $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)')
        ->execute([$username, $hash]);
    echo "Admin '$username' creado (id " . $pdo->lastInsertId() . ").\n";
}
