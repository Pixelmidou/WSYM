<?php
/**
 * lib/html.php - every page shares this <head> and this top bar.
 *
 * Every page prints its <head> and its top bar from here, so the stylesheets, the
 * favicon and the account menu only exist once.
 */

/** Escape text for HTML. Short name so templates stay readable. */
function e($text): string
{
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

/** Where the app is installed, worked out from the running script. '/stuff/wsym' or ''. */
function base(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    $base = $dir === '/' || $dir === '.' ? '' : rtrim($dir, '/');
    return $base;
}

/** A link that works from a sub-folder: url('css/style.css') -> '/stuff/wsym/css/style.css'. */
function url(string $path = ''): string
{
    return base() . '/' . ltrim($path, '/');
}

/** Send the browser somewhere else and stop. redirect('welcome.php') */
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

/** The <head> + opening <body>. $css = extra stylesheet names from /css. */
function page_top(string $title = 'WSYM Banking', array $css = [], bool $bootstrap = true): void
{
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo e($title); ?></title>
    <link rel="shortcut icon" href="<?php echo url('data/favicon.ico'); ?>" type="image/x-icon">
    <?php if ($bootstrap): ?>
    <link rel="stylesheet" href="<?php echo url('bootstrap-5.0.2-dist/css/bootstrap.min.css'); ?>">
    <?php endif; ?>
    <?php foreach ($css as $sheet): ?>
        <link rel="stylesheet" href="<?php echo url('css/' . $sheet . '.css'); ?>">
    <?php endforeach; ?>
    <?php // always last, so it can make the fixed 700px cards fit a phone ?>
    <link rel="stylesheet" href="<?php echo url('css/phone.css'); ?>">
</head>
<body>
    <?php
}

/** Closing scripts + </body>. */
function page_bottom(): void
{
    ?>
    <script src="<?php echo url('bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js'); ?>"></script>
</body>
</html>
    <?php
}

/**
 * The picture shown in an avatar slot.
 * A user who never uploaded a photo gets data/favicon.ico.
 */
function avatar_url(?string $pfp): string
{
    $pfp = trim((string)$pfp);
    if ($pfp === '' || $pfp === 'favicon.ico') {
        return url('data/favicon.ico');
    }
    return url('data/uploads/' . rawurlencode($pfp));
}

/** A card that is dropped straight into <body> (the history page, the settings forms). */
function page_inside(): void
{
    echo '<div class="pageinside">';
}

/** Closes page_inside(). */
function page_inside_end(): void
{
    echo '</div>';
}

/** The answer page: <h1> title, optional detail line, a link back, auto-redirect. */
function show_result(string $title, string $detail = '', string $back = 'welcome.php', int $seconds = 4, bool $ok = true): void
{
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="refresh" content="<?php echo $seconds; ?>; url=<?php echo e($back); ?>">
    <title>WSYM Banking</title>
    <link rel="shortcut icon" href="<?php echo url('data/favicon.ico'); ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo url('css/redirections_style.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('css/phone.css'); ?>">
</head>
<body>
    <div class="container1">
        <div class="container2" style="border-top-color: <?php echo $ok ? '#7ed957' : '#ff8a80'; ?>;">
            <h1 style="text-align: center; font-size: 34px; font-weight: normal;"><?php echo e($title); ?></h1>
            <?php if ($detail !== ''): ?>
                <div style="text-align: center; font-size: medium;"><?php echo $detail; ?></div>
            <?php endif; ?>
            <div style="text-align: center; font-size: small;">
                You will be automatically redirected back in <?php echo $seconds; ?> seconds.
            </div>
            <a href="<?php echo e($back); ?>">Go back now</a>
        </div>
    </div>
</body>
</html>
    <?php
    exit;
}

/**
 * The console sidebar, drawn by admin.php and admin_audit.php so both look the same.
 * $active is one of the tab keys, or "audit" for the log. Below 900px it turns into a
 * menu bar: the "Console menu" button shows and hides the links (css/phone.css).
 */
function admin_sidebar(string $active, string $admin, ?string $pfp): void
{
    $tabs = [
        'home'           => ['Home', 'home'],
        'balances'       => ['Balances', 'dollar'],
        'transactions'   => ['Transactions', 'bank'],
        'blockactions'   => ['Actions Blocking', 'stop'],
        'blockaccount'   => ['Accounts Blocking', 'user'],
        'infomanagement' => ['Info management', 'info'],
        'tickets'        => ['Tickets', 'ticket'],
        'audit'          => ['Audit log', 'clock'],
    ];
    icon_sprite();
    ?>
    <aside class="d-flex flex-column flex-shrink-0 p-3" style="width:280px; background:#9FCAE5">
        <a href="admin.php" class="d-flex align-items-center mb-3 text-decoration-none">
            <img src="<?php echo url('data/favicon.ico'); ?>" width="34" height="34" class="me-2" alt="">
            <span style="font-size:22px;color:#0d3f8f">WSYM Banking</span>
        </a>
        <hr>
        <button type="button" class="menu-toggle" id="consolemenu"
                aria-controls="consoletabs" aria-expanded="false">Console menu</button>
        <ul class="nav nav-pills flex-column mb-auto" id="consoletabs">
            <?php foreach ($tabs as $key => $tab): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $key === $active ? 'active' : 'link-body-emphasis'; ?>"
                       href="<?php echo $key === 'audit' ? 'admin_audit.php' : 'admin.php?tab=' . $key; ?>"><?php echo icon($tab[1]); ?><?php echo $tab[0]; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
        <hr>
        <div class="dropdown dropup">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                <img src="<?php echo avatar_url($pfp); ?>" width="32" height="32" class="rounded-circle me-2" style="object-fit:cover" alt="">
                <strong><?php echo e($admin); ?></strong>
            </a>
            <ul class="dropdown-menu text-small shadow">
                <li><a class="dropdown-item" href="welcome.php">My user page</a></li>
                <li><a class="dropdown-item" href="admin_redirect.php">Redirections</a></li>
                <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
            </ul>
        </div>
    </aside>
    <script>
        // below 900px the sidebar is a bar with this button; above it the button is
        // hidden by css/phone.css and the links are always visible
        document.getElementById('consolemenu').addEventListener('click', function () {
            var open = document.body.classList.toggle('menu-open');
            this.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    </script>
    <?php
}
