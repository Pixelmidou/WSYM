<?php
/**
 * lib/audit.php - "who changed what".
 *
 * One function. Every admin action that changes an account calls it, and
 * admin_audit.php shows the list. Never throws: an audit failure must not break
 * the action the admin asked for.
 */

function audit(string $action, string $target = '', string $details = ''): void
{
    $actor = $_SESSION['user'] ?? 'system';
    db_run('INSERT INTO admin_audit (actor, action, target, details) VALUES (?, ?, ?, ?)',
           [substr($actor, 0, 30), substr($action, 0, 40), substr($target, 0, 100), substr($details, 0, 255)]);
}
