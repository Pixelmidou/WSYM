<?php
/**
 * lib/env.php - read the .env file.
 *
 * Deliberately ~20 lines and no library: one KEY=VALUE per line, "#" starts a comment,
 * quotes are optional, spaces inside a value are kept (Gmail shows App Passwords as
 * "abcd efgh ijkl mnop" and that must not break anything).
 *
 * The file is read as UTF-8 and a leading BOM is dropped: Notepad on Windows likes to add
 * one the first time a file is saved, and without this the first setting name would silently
 * become "\uFEFFdatabase_hostname" and be ignored.
 */

/** All settings, loaded once. Returns an array like ['database_name' => 'wsym', ...] */
function env_all(): array
{
    static $settings = null;
    if ($settings !== null) {
        return $settings;
    }

    $settings = [];
    $file = APP_ROOT . '/.env';
    if (!is_file($file)) {
        return $settings;              // check.php tells the user what to do
    }

    $raw = (string)@file_get_contents($file);
    if (str_starts_with($raw, "\xEF\xBB\xBF")) {
        $raw = substr($raw, 3);                       // UTF-8 BOM, see the note above
    }

    foreach (preg_split('/\R/', $raw) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $value = trim($value);
        // strip one pair of surrounding quotes
        if (strlen($value) > 1 && ($value[0] === '"' || $value[0] === "'") && str_ends_with($value, $value[0])) {
            $value = substr($value, 1, -1);
        }
        $settings[trim($key)] = $value;
    }
    return $settings;
}

/** One setting, with a default. env('mailer_transport', 'file') */
function env(string $key, string $default = ''): string
{
    $all = env_all();
    return $all[$key] ?? $default;
}
