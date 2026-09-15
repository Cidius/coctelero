<?php
declare(strict_types=1);

namespace App;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/helpers.php';

/** Mensajes de contacto/feedback al coctelero. */
final class Messages
{
    public static function create(int $userId, string $name, string $email, string $body, ?int $recipeId): int
    {
        $pdo = Database::get();
        $pdo->prepare(
            'INSERT INTO messages (user_id, recipe_id, name, email, body) VALUES (:u, :r, :n, :e, :b)'
        )->execute([
            ':u' => $userId,
            ':r' => $recipeId,
            ':n' => mb_substr($name, 0, 160),
            ':e' => mb_substr($email, 0, 190),
            ':b' => $body,
        ]);
        return (int) $pdo->lastInsertId();
    }

    /** @return list<array<string,mixed>> */
    public static function all(): array
    {
        return Database::get()->query(
            'SELECT m.*, r.name AS recipe_name, r.slug AS recipe_slug
             FROM messages m
             LEFT JOIN recipes r ON r.id = m.recipe_id
             ORDER BY m.created_at DESC'
        )->fetchAll();
    }

    public static function unreadCount(): int
    {
        return (int) Database::get()->query('SELECT COUNT(*) FROM messages WHERE read_at IS NULL')->fetchColumn();
    }

    public static function markRead(int $id): void
    {
        Database::get()->prepare('UPDATE messages SET read_at = NOW() WHERE id = :id AND read_at IS NULL')
            ->execute([':id' => $id]);
    }

    /**
     * Aviso por mail al admin. Best-effort: si falla o no esta configurado
     * el mensaje ya quedo guardado igual, no se pierde nada.
     */
    public static function notifyAdmin(string $name, string $email, string $body, ?string $recipeName): bool
    {
        $cfg = config()['mail'] ?? [];
        $to = trim((string) ($cfg['admin_to'] ?? ''));
        if ($to === '' || !function_exists('mail')) {
            return false;
        }

        $fromEmail = (string) ($cfg['from_email'] ?? 'no-responder@localhost');
        $fromName  = (string) ($cfg['from_name'] ?? 'El Coctelero Online');

        // Defensa contra inyeccion de headers via campos con salto de linea.
        $strip = static fn(string $s): string => str_replace(["\r", "\n"], '', $s);
        $name = $strip($name);
        $email = $strip($email);

        $subject = $recipeName !== null ? "Nuevo mensaje sobre \"$recipeName\"" : 'Nuevo mensaje de contacto';
        $lines = array_filter([
            "De: $name <$email>",
            $recipeName !== null ? "Receta: $recipeName" : null,
            '',
            $body,
        ], static fn($l) => $l !== null);

        $headers = implode("\r\n", [
            'From: ' . mb_encode_mimeheader($fromName, 'UTF-8') . " <$fromEmail>",
            'Reply-To: ' . $email,
            'Content-Type: text/plain; charset=UTF-8',
        ]);
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

        return @mail($to, $encodedSubject, implode("\n", $lines), $headers);
    }
}
