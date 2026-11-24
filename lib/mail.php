<?php
/**
 * lib/mail.php - sending mail, with four transports.
 *
 *   file   write the mail to a temp folder (default; read it at tools/mailbox.php)
 *   gmail  your own Gmail account with an App Password
 *   smtp   any other server (Mailtrap, Outlook, your host)
 *   mail   PHP mail(), needs sendmail.exe on XAMPP
 *
 * wf_mail() returns true/false and leaves the reason in wf_mail_error().
 *
 * Gmail and SMTP are sent with PHPMailer (lib/phpmailer/, the 3 files it needs, no
 * Composer). Nothing else in the app uses PHPMailer, and nothing else has to change if
 * you swap in your own copy.
 */

/** Where the "file" transport writes. Outside the web root, created on demand. */
function mailbox_dir(): string
{
    $dir = sys_get_temp_dir() . '/wsym_mailbox';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    return $dir;
}

/** The From: address, first usable candidate. Gmail refuses anything but your own address. */
function mail_from(): string
{
    $candidates = [
        env('mailer_transport') === 'gmail' ? env('mailer_username') : '',
        env('email_verif_address'),
        env('mailer_username'),
        'no-reply@wsym.local',
    ];
    foreach ($candidates as $candidate) {
        if (filter_var(trim($candidate), FILTER_VALIDATE_EMAIL)) {
            return trim($candidate);
        }
    }
    return 'no-reply@wsym.local';
}

/** Remember why the last send failed ('' = it worked). */
function wf_mail_error(?string $set = null): string
{
    static $error = '';
    if ($set !== null) {
        $error = $set;
    }
    return $error;
}

/**
 * Send one HTML mail.
 * Returns true when it was accepted by the chosen transport.
 */
function wf_mail(string $to, string $subject, string $html): bool
{
    wf_mail_error('');
    $transport = strtolower(env('mailer_transport', 'file'));

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        wf_mail_error('That is not a valid email address.');
        return false;
    }

    if ($transport === 'file') {
        return mail_to_file($to, $subject, $html);
    }
    if ($transport === 'mail') {
        $headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n"
                 . 'From: ' . mail_from() . "\r\n";
        $ok = @mail($to, $subject, $html, $headers);
        if (!$ok) {
            wf_mail_error('PHP mail() failed - XAMPP has no sendmail by default. Use mailer_transport=file or gmail.');
        }
        return $ok;
    }

    // gmail and smtp both go through PHPMailer; only host/port/encryption differ
    return mail_phpmailer($to, $subject, $html);
}

/**
 * Gmail / SMTP with PHPMailer.
 *
 * Gmail wants: smtp.gmail.com, an App Password (not your normal password),
 * port 465 with `ssl` or port 587 with `tls`, and a From: that is your own address.
 * All of that comes from .env - see README section 3.
 */

/** Load lib/phpmailer once (no Composer). False + error message when a file is gone. */
function phpmailer_load(): bool
{
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        return true;
    }
    $base = __DIR__ . '/phpmailer/src/';
    foreach (['Exception.php', 'PHPMailer.php', 'SMTP.php'] as $file) {
        if (!is_file($base . $file)) {
            wf_mail_error('PHPMailer is missing: lib/phpmailer/src/' . $file
                        . ' not found. Unzip the archive again.');
            return false;
        }
        require_once $base . $file;
    }
    return class_exists('PHPMailer\\PHPMailer\\PHPMailer');
}

function mail_phpmailer(string $to, string $subject, string $html): bool
{
    if (!phpmailer_load()) {
        return false;
    }

    $transport = strtolower(env('mailer_transport', 'gmail'));
    $host      = $transport === 'gmail' ? 'smtp.gmail.com' : env('mailer_host', '');
    $secure    = strtolower(env('mailer_secure', 'ssl'));       // ssl | tls | (empty)
    $port      = (int)env('mailer_port', $secure === 'tls' ? '587' : '465');
    $user      = env('mailer_username');
    $pass      = str_replace(' ', '', env('mailer_password'));  // App Passwords print in groups of four
    $from      = mail_from();

    if ($host === '') {
        wf_mail_error('mailer_host is empty in .env (only needed when mailer_transport=smtp).');
        return false;
    }

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host        = $host;
        $mail->Port        = $port;
        $mail->SMTPAuth    = $user !== '';
        $mail->Username    = $user;
        $mail->Password    = $pass;
        $mail->SMTPSecure  = $secure === 'tls' ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS
                        : ($secure === 'ssl' ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
                        : '');
        $mail->Timeout     = (int)env('mailer_timeout', '20');
        $mail->CharSet     = 'UTF-8';
        $mail->setFrom($from, env('mailer_from_name', 'WSYM Banking'));
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html;
        $mail->AltBody = trim(strip_tags(str_replace(['<br>', '</p>'], "\n", $html)));
        $mail->send();
        return true;
    } catch (Throwable $e) {
        $why = $mail->ErrorInfo !== '' ? $mail->ErrorInfo : $e->getMessage();
        if (stripos($why, 'authenticate') !== false || stripos($why, '535') !== false) {
            $why .= ' - for Gmail this almost always means "use an App Password, not your normal password".';
        }
        wf_mail_error('PHPMailer: ' . $why);
        return false;
    }
}

/** Write the mail into the temp mailbox so it can be read with no internet. */
function mail_to_file(string $to, string $subject, string $html): bool
{
    $dir = mailbox_dir();
    if (!is_dir($dir) && !@mkdir($dir, 0777, true)) {
        wf_mail_error('The temp mailbox folder is not writable.');
        return false;
    }

    $stamp = date('Y-m-d_H-i-s');
    $safe  = preg_replace('/[^A-Za-z0-9@._-]/', '_', $to) ?: 'unknown';
    $file  = "$dir/{$stamp}_{$safe}.html";
    for ($n = 2; file_exists($file); $n++) {          // two mails in one second must not collide
        $file = "$dir/{$stamp}_{$safe}~{$n}.html";
    }

    $links = [];
    if (preg_match_all('#https?://[^\s"\'<>]+#', $html, $m)) {
        $links = array_values(array_unique($m[0]));
    }

    $page = "<!DOCTYPE html><html><head><meta charset='utf-8'><title>" . htmlspecialchars($subject)
          . "</title></head><body style=\"font-family:Segoe UI,sans-serif\">"
          . "<p style='background:#eef4ff;padding:10px;border-radius:6px'><b>To:</b> "
          . htmlspecialchars($to) . "<br><b>Subject:</b> " . htmlspecialchars($subject) . "</p>"
          . $html
          . "<hr><p><b>Links in this message:</b></p><ul>";
    foreach ($links as $link) {
        $page .= "<li><a href=\"" . htmlspecialchars($link) . "\">" . htmlspecialchars($link) . "</a></li>";
    }
    $page .= '</ul></body></html>';

    if (@file_put_contents($file, $page) === false) {
        wf_mail_error('Could not write to the temp mailbox folder.');
        return false;
    }
    return true;
}

/* ------------------------------------------------------- the two real mails */

/** Verification mail. Returns the link that was emailed. */
function mail_verification(string $username, string $to, string $token): string
{
    $link = base_url() . '/verify.php?token=' . $token;
    $html = '<p>Hello ' . htmlspecialchars($username) . ',</p>'
          . '<p>Welcome to WSYM Banking. Click the link below to verify your email address:</p>'
          . '<p><a href="' . htmlspecialchars($link) . '">Verify my email</a></p>'
          . '<p>The link works for 24 hours.</p>'
          . '<p>If you did not create this account, ignore this message.</p>';
    wf_mail($to, 'WSYM - verify your email', $html);
    return $link;
}

/** "Forgot password" mail. Returns the link that was emailed. */
function mail_password_reset(string $username, string $to, string $token): string
{
    $link = base_url() . '/reset.php?token=' . $token;
    $html = '<p>Hello ' . htmlspecialchars($username) . ',</p>'
          . '<p>Somebody asked to reset the password of this account. Click here to choose a new one:</p>'
          . '<p><a href="' . htmlspecialchars($link) . '">Choose a new password</a></p>'
          . '<p>The link works once, for 30 minutes. If it was not you, ignore this message.</p>';
    wf_mail($to, 'WSYM - password reset', $html);
    return $link;
}

/** The absolute address of this install, for links inside emails. */
function base_url(): string
{
    if (env('base_url') !== '') {
        return rtrim(env('base_url'), '/');
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . base();
}
