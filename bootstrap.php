<?php
/**
 * bootstrap.php - one include at the top of every page.
 *
 *     require __DIR__ . '/bootstrap.php';
 *
 * It loads the settings, the database helpers and the small libraries. Nothing here
 * echoes anything, so a page is free to redirect before it prints.
 */

define('APP_ROOT', __DIR__);

// Before anything else: if the browser asks for a design image that is not on disk,
// answer it here instead of letting it 404. A real file on disk still wins.
require_once APP_ROOT . '/lib/missing_images.php';
serve_missing_image();

require_once APP_ROOT . '/lib/env.php';
require_once APP_ROOT . '/lib/db.php';
require_once APP_ROOT . '/lib/html.php';
require_once APP_ROOT . '/lib/icons.php';
require_once APP_ROOT . '/lib/auth.php';
require_once APP_ROOT . '/lib/money.php';
require_once APP_ROOT . '/lib/mail.php';
require_once APP_ROOT . '/lib/tickets.php';
require_once APP_ROOT . '/lib/history.php';
require_once APP_ROOT . '/lib/audit.php';
require_once APP_ROOT . '/lib/accounts.php';

// A POST that changes something always carries the session token.
// (session_boot() is called here too, so a POST page and the page that drew its form
// always use the same session folder - see the note in lib/auth.php.)
session_boot();
check_csrf();
