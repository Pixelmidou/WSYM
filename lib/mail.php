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
 * Nothing here needs Composer: the mail is built as plain text/HTML and sent over a
 * socket, which is all PHPMailer did for us.
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

    // gmail and smtp both speak SMTP; only the host/port/encryption differ
    return mail_smtp($to, $subject, $html);
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

/**
 * Minimal SMTP client: connect, EHLO, STARTTLS or SSL, AUTH LOGIN, MAIL, RCPT, DATA.
 * Enough for Gmail, Mailtrap and any normal provider; no dependencies.
 */
function mail_smtp(string $to, string $subject, string $html): bool
{
    $host   = env('mailer_transport') === 'gmail' ? 'smtp.gmail.com' : env('mailer_host', '');
    $port   = (int)env('mailer_port', '465');
    $secure = strtolower(env('mailer_secure', 'ssl'));       // ssl | tls | (empty)
    $user   = env('mailer_username');
    $pass   = str_replace(' ', '', env('mailer_password'));  // Google shows the App Password in groups of four
    $from   = mail_from();

    if ($host === '') {
        wf_mail_error('mailer_host is empty in .env.');
        return false;
    }

    $remote = ($secure === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
    $socket = @stream_socket_client($remote, $errno, $errstr, (int)env('mailer_timeout', '15'));
    if (!$socket) {
        wf_mail_error("Cannot connect to $host:$port - $errstr. "
                    . 'If this is port 465, check mailer_secure=ssl; if 587, use mailer_secure=tls.');
        return false;
    }
    stream_set_timeout($socket, (int)env('mailer_timeout', '15'));

    $read = function () use ($socket): string {
        $data = '';
        while (($line = fgets($socket, 515)) !== false) {
            $data .= $line;
            if (strlen($line) < 4 || $line[3] === ' ') {
                break;
            }
        }
        return $data;
    };
    $say = function (string $command) use ($socket, &$read): string {
        fwrite($socket, $command . "\r\n");
        return $read();
    };

    if (!str_starts_with($read(), '220')) {
        wf_mail_error('The mail server did not greet us. Wrong host or port?');
        fclose($socket);
        return false;
    }
    $reply = $say('EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'));

    if ($secure === 'tls') {
        $reply = $say('STARTTLS');
        if (!str_starts_with($reply, '220')) {
            wf_mail_error("STARTTLS was refused: " . trim($reply));
            fclose($socket);
            return false;
        }
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            wf_mail_error('TLS handshake failed. mailer_secure=tls needs port 587.');
            fclose($socket);
            return false;
        }
        $say('EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
    }

    if ($user !== '') {
        $say('AUTH LOGIN');
        $say(base64_encode($user));
        $reply = $say(base64_encode($pass));
        if (!str_starts_with($reply, '235')) {
            wf_mail_error('The server rejected the login (' . trim($reply) . '). '
                        . 'For Gmail this almost always means "use an App Password, not your normal password".');
            fclose($socket);
            return false;
        }
    }

    $reply = $say('MAIL FROM:<' . $from . '>');
    if (!str_starts_with($reply, '250')) {
        wf_mail_error('Sender refused (' . trim($reply) . '). In Gmail mode the From: must be your own address.');
        fclose($socket);
        return false;
    }
    $reply = $say('RCPT TO:<' . $to . '>');
    if (!str_starts_with($reply, '250') && !str_starts_with($reply, '251')) {
        wf_mail_error('Recipient refused (' . trim($reply) . ').');
        fclose($socket);
        return false;
    }
    $say('DATA');

    $headers = 'From: ' . env('mailer_from_name', 'WSYM Banking') . ' <' . $from . ">\r\n"
             . 'To: <' . $to . ">\r\n"
             . 'Subject: =?UTF-8?B?' . base64_encode($subject) . "?=\r\n"
             . 'Date: ' . date('r') . "\r\n"
             . "MIME-Version: 1.0\r\n"
             . "Content-Type: text/html; charset=UTF-8\r\n"
             . "Content-Transfer-Encoding: base64\r\n";
    $body = chunk_split(base64_encode($html), 76, "\r\n");

    $reply = $say($headers . "\r\n" . $body . '.');     // a lone "." ends the data
    fclose($socket);

    if (!str_starts_with($reply, '250')) {
        wf_mail_error('The server refused the message: ' . trim($reply));
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
