<?php
/**
 * tools/mailbox.php - read the mails captured by mailer_transport=file.
 *
 * It is a normal app page, so it needs a login. A normal user only ever sees the
 * mail that was addressed to their own account; an admin sees every captured mail.
 */
require __DIR__ . '/../bootstrap.php';

$me     = require_login();
$mine   = (string)(user_row($me)['email'] ?? '');
$isAdm  = !empty($_SESSION['admin']);
$dir    = mailbox_dir();

/** All captured files, newest first, limited to what this visitor may read. */
function mailbox_files(string $dir, bool $isAdm, string $mine): array
{
    $files = glob($dir . '/*.html') ?: [];
    if (!$isAdm) {
        $files = array_values(array_filter($files, function ($file) use ($mine) {
            return $mine !== '' && str_contains(basename($file), '_' . $mine . '.html');
        }));
    }
    usort($files, fn($a, $b) => filemtime($b) <=> filemtime($a));
    return $files;
}

$files = mailbox_files($dir, $isAdm, $mine);

$open = basename((string)($_GET['open'] ?? ''));
$body = '';
foreach ($files as $file) {                       // only files this visitor may read
    if ($open !== '' && basename($file) === $open) {
        $body = (string)file_get_contents($file);
    }
}

page_top('WSYM - local mailbox', []);
?>
<div class="container py-4" style="max-width:900px">
    <h1 class="h4">Local mailbox</h1>
    <p class="text-muted">
        Every mail the app "sent" while <code>mailer_transport=file</code>, newest first.
        <?php if ($isAdm): ?>
            You are an admin, so this is <b>every</b> captured mail.
        <?php else: ?>
            You only see the mail addressed to <b><?php echo e($mine); ?></b>.
        <?php endif; ?>
        Folder: <code><?php echo e($dir); ?></code> — <a href="mailbox.php">refresh</a>.
    </p>

    <?php if (!$files): ?>
        <div class="alert alert-secondary">
            <?php if ($isAdm): ?>
                No mail yet. Register an account or use "I forgot my password".
            <?php else: ?>
                Nothing here yet. Mail sent to your address will appear on this page.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <ul class="list-group mb-4">
        <?php foreach ($files as $file): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><?php echo e(date('Y-m-d H:i:s', filemtime($file))); ?> &middot; <?php echo e(basename($file)); ?></span>
                <a class="btn btn-sm btn-primary" href="mailbox.php?open=<?php echo urlencode(basename($file)); ?>">open</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($body !== ''): ?>
        <div class="card p-3">
            <div class="d-flex justify-content-between mb-2">
                <b><?php echo e($open); ?></b>
                <a class="btn btn-sm btn-outline-secondary" href="mailbox.php">close</a>
            </div>
            <?php
            // the captured file is written by this app; scripts are stripped anyway
            echo preg_replace('#<script\b[^>]*>.*?</script>#is', '', $body);
            ?>
        </div>
    <?php endif; ?>
</div>
<?php page_bottom(); ?>
