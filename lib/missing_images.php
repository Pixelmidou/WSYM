<?php
/**
 * lib/missing_images.php - stand-ins for the design images, in case one goes missing.
 *
 * Required at the very top of bootstrap.php, before anything else. If the browser asks
 * for one of the images below and the file is not on disk, the icon is drawn, printed as
 * a PNG and the request stops there. A missing image then shows the icon the design
 * wanted instead of a broken-image box.
 *
 * Only missing files are intercepted: a real file on disk is served by Apache (or PHP's
 * own server) as usual, so your own version in data/ always wins.
 */

/** name => [svg markup, width, height]. Keep it small: these are 16-20px icons. */
function missing_image_icons(): array
{
    // one shared colour, the same pale blue the other icons in data/ use
    $blue = '#8ab4d8';
    return [
        // the "stop / block" icon of the admin console sidebar
        'stop.svg' => [
            '<circle cx="12" cy="12" r="9" fill="none" stroke="' . $blue . '" stroke-width="2"/>'
            . '<path d="M8 12h8" stroke="' . $blue . '" stroke-width="2" stroke-linecap="round"/>',
            24, 24,
        ],
        // the "write / message" icon
        'write.svg' => [
            '<path d="M3 21l1.2-4.3L16.5 4.4a2 2 0 0 1 2.8 0l.3.3a2 2 0 0 1 0 2.8L7.3 19.8 3 21z" fill="none" stroke="' . $blue . '" stroke-width="2" stroke-linejoin="round"/>',
            24, 24,
        ],
        // the "calendar" icon of the admin forms
        'calender.svg' => [
            '<rect x="3" y="5" width="18" height="16" rx="2" fill="none" stroke="' . $blue . '" stroke-width="2"/>'
            . '<path d="M3 10h18M8 3v4M16 3v4" stroke="' . $blue . '" stroke-width="2" stroke-linecap="round"/>',
            24, 24,
        ],
        // the "clock" icon of the admin forms
        'clock.svg' => [
            '<circle cx="12" cy="12" r="9" fill="none" stroke="' . $blue . '" stroke-width="2"/>'
            . '<path d="M12 7v5l3.5 2" fill="none" stroke="' . $blue . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
            24, 24,
        ],
    ];
}

/**
 * Serve one missing image if that is what was asked for, then exit.
 * Called from bootstrap.php before sessions and the database are touched.
 */
function serve_missing_image(): void
{
    $script = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
    if (!preg_match('~/data/([a-z0-9_.-]+\.(?:svg|png|jpg))$~i', $script, $m)) {
        return;
    }
    $name = strtolower($m[1]);
    $file = APP_ROOT . '/data/' . $name;

    if (is_file($file)) {
        return;                      // the real file: let the web server send it
    }
    $icons = missing_image_icons();
    if (!isset($icons[$name])) {
        http_response_code(404);
        exit;                        // a genuine typo should stay a 404
    }

    [$markup, $w, $h] = $icons[$name];
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $w . ' ' . $h . '"'
         . ' width="' . $w . '" height="' . $h . '">' . $markup . '</svg>';

    header('Content-Type: image/svg+xml');
    header('Cache-Control: public, max-age=86400');
    echo $svg;
    exit;
}
