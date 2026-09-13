<?php
declare(strict_types=1);

require __DIR__ . '/_common.php';
require_once __DIR__ . '/../src/Messages.php';

use App\Auth;
use App\Messages;

use function App\e;
use function App\url;

Auth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        Messages::markRead($id);
    }
    header('Location: ' . url('admin/mensajes.php'));
    exit;
}

$messages = Messages::all();

admin_header('Mensajes');
?>
<div class="page-head">
    <h1>Mensajes <span class="muted">(<?= count($messages) ?>)</span></h1>
</div>

<?php if ($messages === []): ?>
    <p class="muted">No llegó ningún mensaje todavía.</p>
<?php endif; ?>

<?php foreach ($messages as $m): ?>
    <?php $unread = $m['read_at'] === null; ?>
    <div class="msg-card<?= $unread ? ' is-unread' : '' ?>">
        <div class="msg-head">
            <div>
                <strong><?= e($m['name']) ?></strong>
                <a class="muted small" href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a>
                <?php if (!empty($m['recipe_name'])): ?>
                    · <a href="<?= e(url('receta.php?slug=' . urlencode($m['recipe_slug']))) ?>" target="_blank" rel="noopener">
                        <?= e($m['recipe_name']) ?> ↗
                    </a>
                <?php endif; ?>
            </div>
            <span class="muted small"><?= e(substr((string) $m['created_at'], 0, 16)) ?></span>
        </div>
        <p class="msg-body"><?= nl2br(e($m['body'])) ?></p>
        <?php if ($unread): ?>
            <form method="post" action="<?= e(url('admin/mensajes.php')) ?>">
                <?= Auth::csrfField() ?>
                <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                <button type="submit" class="link">Marcar leído</button>
            </form>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
<?php
admin_footer();
