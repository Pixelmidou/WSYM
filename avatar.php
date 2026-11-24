<?php
/**
 * avatar.php - upload or delete the profile picture.
 *
 * The picture is turned into a square image that fills the 32x32 slot: it is scaled to
 * cover the square and the overflow is cropped off, exactly like CSS object-fit: cover.
 *
 * Transparent pictures stay transparent. A PNG or GIF with a see-through background is
 * saved as a PNG with its alpha channel intact, so the header shows the photo cut-out on
 * the blue bar instead of a white disc around it. Only real photos (JPEG) get a solid
 * background, because JPEG cannot store transparency.
 */
require __DIR__ . '/bootstrap.php';
$username = require_login();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('welcome.php');
}

$dir = APP_ROOT . '/data/uploads';
if (!is_dir($dir)) {
    @mkdir($dir, 0755, true);
}

/* ------------------------------------------------------------- delete */
if (isset($_POST['imgdel'])) {
    $current = (string)db_val('SELECT pfp FROM login_credentials WHERE username = ?', [$username], '');
    if ($current === '' || $current === 'favicon.ico') {
        show_result('Action Failed : Image is not in our database', '', 'welcome.php', 4, false);
    }
    @unlink($dir . '/' . basename($current));
    db_run('UPDATE login_credentials SET pfp = ? WHERE username = ?', ['favicon.ico', $username]);
    show_result('Action Success : Image Deleted !', 'Your picture is the WSYM logo again.', 'welcome.php', 4, true);
}

/* ------------------------------------------------------------- upload */
$file = $_FILES['imgupload'] ?? null;
if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
    show_result('Action Failed : Choose a file !', '', 'welcome.php', 4, false);
}
if ($file['error'] !== UPLOAD_ERR_OK) {
    show_result('Action Failed : The file did not arrive', 'If it is big, raise upload_max_filesize in php.ini.', 'welcome.php', 4, false);
}
if ($file['size'] > 1000000) {
    show_result('Action Failed : Image must not exceed 1MB !', '', 'welcome.php', 4, false);
}

// the bytes decide, not the file name: a .png full of PHP is not an image
$info = @getimagesize($file['tmp_name']);
$allowed = [IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_GIF => 'gif'];
if ($info === false || !isset($allowed[$info[2]])) {
    show_result('Action Failed : File must be a valid image with (png, jpg, jpeg, gif) as extension !',
                '', 'welcome.php', 5, false);
}

$old = (string)db_val('SELECT pfp FROM login_credentials WHERE username = ?', [$username], '');
$type = $info[2];

/** Everything that keeps the picture a square: GD if it is there, otherwise copy the file. */
function save_avatar(string $tmp, string $dir, string $username, int $type, int $width, int $height): ?string
{
    // No GD on this server? Store the upload unchanged - CSS crops it the same way.
    if (!function_exists('imagecreatetruecolor')) {
        $target = $dir . '/' . $username . '.' . ['', '', 'jpg', 'png', 'gif'][$type];
        return move_uploaded_file($tmp, $target) ? basename($target) : null;
    }

    $source = match ($type) {
        IMAGETYPE_PNG  => @imagecreatefrompng($tmp),
        IMAGETYPE_GIF  => @imagecreatefromgif($tmp),
        default        => @imagecreatefromjpeg($tmp),
    };
    if ($source === false) {
        return null;
    }

    $side = 256;
    $canvas = imagecreatetruecolor($side, $side);

    // a GIF/PNG keeps its transparency, a JPEG gets a plain white square instead of black
    $transparent = $type !== IMAGETYPE_JPEG;
    if ($transparent) {
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
    } else {
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
    }

    // scale so the picture COVERS the square (max, not min), then centre it: the extra
    // part hangs over the edge and is simply not copied - that is the crop.
    $scale = max($side / max($width, 1), $side / max($height, 1));
    $nw = max(1, (int)round($width * $scale));
    $nh = max(1, (int)round($height * $scale));
    if ($transparent) {
        imagealphablending($canvas, true);   // let the resampled pixels blend their alpha
    }
    imagecopyresampled($canvas, $source, (int)(($side - $nw) / 2), (int)(($side - $nh) / 2),
                       0, 0, $nw, $nh, $width, $height);

    // the extension follows the pixels: PNG in, PNG out (so the alpha survives)
    $name = $username . ($transparent ? '.png' : '.jpg');
    $path = $dir . '/' . $name;
    $ok = $transparent ? @imagepng($canvas, $path) : @imagejpeg($canvas, $path, 88);

    imagedestroy($canvas);
    imagedestroy($source);

    return $ok ? $name : null;
}

$name = save_avatar($file['tmp_name'], $dir, $username, $type, (int)$info[0], (int)$info[1]);

if ($name === null) {
    show_result('Error 500 : Internal Server Error',
                'data/uploads is not writable - on Windows, right-click it > Properties > uncheck Read-only.',
                'welcome.php', 6, false);
}

// the new picture has a different extension than the old one: drop the old file
if ($old !== '' && $old !== 'favicon.ico' && $old !== $name) {
    @unlink($dir . '/' . basename($old));
}

db_run('UPDATE login_credentials SET pfp = ? WHERE username = ?', [$name, $username]);
show_result('Action Success : Image Uploaded !', 'Your new picture is in the corner of every page.', 'welcome.php', 4, true);
