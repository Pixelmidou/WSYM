# WSYM — A Banking System Simulator

A fictional banking system, built as a hands-on PHP/MySQL portfolio project. WSYM shows
account flows, simulated transactions and both the customer and the administrator side of
a bank.

> **Try it live:** <https://wsym.nysr.host/index.php>
>
> **This is a simulation.** No real banking, no real money, no payments, no financial
> services. Balances and transactions exist only in the demo database.

* Plain **PHP 8 + MySQLi**, no framework, no Composer, no Python, no shell scripts, no
  `composer install` step.
* **Bootstrap 5.0.2**, **Font Awesome 4.7** and **PHPMailer 6.9.3** ship in the folder, so it
  works offline and there is nothing to install.
* Runs on **XAMPP / Windows** in any sub-folder (the app works out its own URL).
* Every page fits a phone as well as a desktop.

---

## 1. Features

**Customer**

* Register, log in, verify the email, resend the verification link.
* Forgot password → single-use reset link by email.
* Deposit, withdraw and wire money (simulated), with five quick "$10…$50" buttons.
* Balance, last movement, full history with paging, and a **CSV statement** for Excel.
* Support as a **conversation**: chat bubbles, reply in place, close, and the earlier
  conversation stays readable.
* Change username / email / password (the password is asked for again first).
* Profile picture upload, cropped square, transparent backgrounds kept transparent.

**Administrator** — one console, seven tabs

| Tab | What it does |
|---|---|
| Home | number of clients, money stored, what moved in the last 24 hours |
| Balances | find a client (by name or email), see and change their balance |
| Transactions | a client's deposits, withdrawals or wires |
| Actions Blocking | allow or block deposit / withdraw / wire / tickets, per client |
| Accounts Blocking | disable or re-enable a whole account |
| Info management | client list; force re-verification, force a password reset, end a session, rename |
| Tickets | read the customer conversations, answer and close them |
| Audit log | every administrator action, newest first, filterable |

Also: forced logout by an administrator, "remember me", 90-minute inactivity timeout,
CSRF token on every form, prepared statements everywhere, bcrypt password hashes, and a
setup doctor (`check.php`) that lists every setting and file with a plain-English fix.

---

## 2. Install on XAMPP (Windows)

1. Copy the whole `wsym` folder into **`C:\xampp\htdocs\stuff\wsym`**
   (rename it or move it, the code works out its own URL).
2. Start **Apache** and **MySQL** in the XAMPP control panel.
3. Open <http://localhost/phpmyadmin>, click **Import**, choose **`wsym.sql`**, click **Go**.
   That creates the database `wsym` with its 10 tables and three demo accounts.
4. Open **`wsym\.env.example`**, save a copy **in the same folder** called **`.env`**
   (Windows hides dot-files: File Explorer → *View* → tick *Hidden items*).
   The defaults are already right for a stock XAMPP: user `root`, empty password.
5. Open <http://localhost/stuff/wsym/check.php>. It checks every setting and file and
   prints the exact fix for anything that is wrong. When it is green:

   <http://localhost/stuff/wsym/> — log in and try it.

**Never import `wsym.sql` into a database that holds data you want to keep**: it drops and
recreates its tables.

### Demo accounts (created by `wsym.sql`)

| Username | Password | What it is for |
|---|---|---|
| `VerifiedUser` | `VerifiedUser123` | a normal, email-verified customer |
| `nonVerifiedUser` | `nonVerifiedUser123` | cannot move money until the email is verified |
| `Admin` | `Admin123` | the administrator console |

New accounts can be created from the login page; they receive a verification email.

The same three accounts work on the hosted demo: <https://wsym.nysr.host/index.php>

---

## 3. Email — pick one of three ways

The app sends three kinds of mail: the verification link after registering, the
"forgot password" link, and the optional test message from `tools/mail_test.php`.
**All of them go through one function**, `wf_mail()` in `lib/mail.php`, which reads
`mailer_transport` from `.env`. That value is the only thing you change — no code edits,
and switching never breaks anything else.

| `mailer_transport` | What happens to the mail | Needs internet? | Where you read it |
|---|---|---|---|
| `file` *(default)* | Written to a folder on the server, never sent | No | `tools/mailbox.php` inside the app |
| `smtp` | Handed to an SMTP server — your own on `127.0.0.1` **or** a remote host | Only if the host is remote | That server's inbox / mail catcher |
| `gmail` | Sent through Gmail with an App Password | Yes | Your Gmail account |
| `mail` | PHP `mail()` → local `sendmail` | No | The host's local mailbox |

Two rules that hold for every option:

* If sending fails the app **never shows a blank page**: the reason is printed on the
  page (and on the verification/reset screens), so you always know why no mail arrived.
* `check.php` shows the active transport, and `tools/mail_test.php` sends one message and
  reports exactly what the server answered.

**In one line:** keep `file` if you just want the demo to work with no setup; use `smtp`
with `mailer_host=127.0.0.1` if the mails must land in a real mailbox on your own server;
use `gmail` if the mails must reach real outside addresses. Nothing else in the app changes.

---

### Option A — files on disk (default, no setup, works offline)

```dotenv
mailer_transport=file
```

Every message is written as an `.html` file to the system temp folder
(`%TEMP%\wsym_mailbox` on XAMPP, `/tmp/wsym_mailbox` elsewhere) and you read it at
**`tools/mailbox.php`**, newest first. Verification and reset links are live inside it.

That page is **not public**: it requires a login, and then

* a **normal user only sees the mail addressed to their own account** — the file list is
  filtered by the address in their profile, and asking for another user's file by editing
  the `?open=` name shows nothing;
* an **admin sees every captured message**;
* a logged-out visitor is sent to the login page, and `?open=../../etc/passwd` is ignored.

This is the right choice for a demo, a portfolio page or a mark on your recording: nothing
leaves the machine, so nothing can leak.

> Honest caveat: with `file` the messages are plain files on the server, readable by anyone
> with FTP/SSH access to that folder — and by every admin of the app. It is a filing
> cabinet, not a private mailbox. If you need real per-person privacy, use Option B.

---

### Option B — a mail server on the same machine (what you asked about)

Keep `smtp` and point `mailer_host` at the machine itself. The app connects to the relay
over loopback, the relay decides what happens next: keep the message in a local mailbox,
deliver it to a real address, or just show it in a catcher UI. **This is the "emails arrive
there, only that person can see them" setup**, because each person then reads their own
mail in their own mailbox — the app itself keeps no copy.

```dotenv
mailer_transport=smtp
mailer_host=127.0.0.1
mailer_port=1025          # 1025 MailHog/Mailpit, 25 Postfix/hMailServer, 2525 smtp4dev
mailer_secure=            # empty = plain; tls only for a real server on 587
mailer_username=          # empty = no login at all
mailer_password=
```

Pick the port that matches your relay:

| Relay on that machine | `mailer_port` | `mailer_secure` | Username / password |
|---|---|---|---|
| Mailpit | `1025` | *(empty)* | *(empty)* |
| MailHog | `1025` | *(empty)* | *(empty)* |
| smtp4dev (Windows) | `2525` | *(empty)* | *(empty)* |
| hMailServer / Mailenable (local, no TLS) | `25` | *(empty)* | usually a real account |
| Real Postfix/Exim with STARTTLS | `587` | `tls` | the account it expects |
| Real Postfix/Exim with implicit TLS | `465` | `ssl` | the account it expects |

All of those shapes are tested: plain, STARTTLS and implicit SSL each come back
`sent: true`, and with `mailer_username` empty the app sends **no AUTH at all**, so a relay
that does not ask for a login is happy.

> **Careful:** `.env.example` ships with `mailer_port=465` and `mailer_secure=ssl` for
> Gmail. If you switch to `smtp` you must set the port and the encryption too — changing
> only `mailer_transport` leaves it dialling 465 with TLS, which a catcher on 1025 will
> refuse. This is the single most common reason a local relay "does not work".

Details worth knowing:

* `mailer_timeout` (default `20` seconds) stops a dead relay from freezing the page; when
  it expires you get `Could not connect to SMTP host` instead of a spinner.
* `From:` falls back to `no-reply@wsym.local` when no other address is usable — every local
  relay accepts that; a real remote server may not.
* A relay with **no TLS support** will refuse `mailer_secure=tls`, and one with a
  **self-signed certificate** will fail the certificate check. Use plain SMTP for a catcher
  and you will never hit either.
* On a hosted box (nysr, cPanel, a VPS) this all happens over the machine's own loopback,
  so **you never have to open an SMTP port to the internet** — and Gmail's rate limits are
  not involved.
* If the relay does want a login, fill `mailer_username` / `mailer_password`: that flips
  PHPMailer's `SMTPAuth` on automatically.

---

### Option C — send it for real through Gmail

1. Google Account → **Security** → turn on **2-Step Verification**.
2. **App passwords** → create one for "Mail" → copy the 16 characters.
3. In `.env`:

   ```dotenv
   mailer_transport=gmail
   mailer_username=your.address@gmail.com
   mailer_password=abcdefghijklmnop
   mailer_port=465
   mailer_secure=ssl
   ```

4. Open **`tools/mail_test.php`**, type a recipient, press *Send*. It either says the mail
   was accepted or shows the SMTP error. The usual ones:

   * `Could not authenticate` / `535` → you used the normal Gmail password. Use the
     16-character App Password (spaces are stripped for you).
   * `Could not connect to SMTP host` → the network or host is blocking 465/587.
   * `From:` is forced to `mailer_username` in this mode, because Gmail refuses to send as
     an address you do not own.

`gmail` **ignores `mailer_host`** (verified: pointing it at `127.0.0.1` still goes to
Gmail), so you cannot half-switch between Option B and C by accident.

---

### Why the third one, `mail`, is there

`mailer_transport=mail` uses PHP's own `mail()`, which needs a working `sendmail` on the
machine. On XAMPP that is normally **not** configured and the call simply fails — the app
reports it instead of hanging. Use Option A or B rather than this one unless you know
`sendmail.exe` is set up.

### And if none of them work

`lib/mail.php` catches the failure, stores a one-line reason, and the page shows it. Gmail
and SMTP are sent with **PHPMailer 6.9.3**, which is already in the folder
(`lib/phpmailer/src/`, three files, no Composer, nothing to install). If those files are
deleted, `check.php` turns three rows red and the error message says which file is missing.

### What the app does not do

It only sends. There is no in-app inbox except `tools/mailbox.php` for Option A, and no
copy of anything sent with `gmail`, `smtp` or `mail` — read those in the destination
mailbox. One install also shares one temp folder, so it assumes one install = one business.

---

## 4. The `.env` file

```dotenv
database_hostname=localhost
database_username=root
database_password=
database_name=wsym

mailer_transport=file          # file | smtp | gmail | mail   (see section 3)
mailer_username=               # empty = no AUTH at all (fine for a local relay)
mailer_password=               # Gmail mode: the 16-character App Password
mailer_host=127.0.0.1          # used by smtp only; '' = smtp.gmail.com
mailer_port=465                # 1025 catcher, 25/587/465 real server, 465 Gmail
mailer_secure=ssl              # '' plain | tls (587) | ssl (465)

base_url=                      # empty = worked out from the request
```

`.env` is in `.gitignore` and is never part of the archive. Start from `.env.example`.

---

## 5. What is in the folder

```
wsym/
  index.php        login / create account / forgot password (one page, three panels)
  welcome.php      the customer dashboard: balance, deposit, withdraw, wire
  history.php      full movement list, filter by type, 10 per page
  statement.php    the same data as a CSV file (opens in Excel)
  tickets.php      message conversation with support
  settings.php     change username / email / password (asks for the password first)
  avatar.php       upload or delete the profile picture (cropped to 256x256)
  admin.php        the console: 7 tabs, one file
  admin_audit.php  every administrator action, newest first
  check.php        setup doctor: every setting and file, with a plain-English fix
  lib/             the whole logic, one small file per subject (see below)
  lib/phpmailer/   PHPMailer 6.9.3, used for Gmail and SMTP (already included)
  css/ js/ data/   the design: stylesheets (phone.css = narrow screens), scripts, images
  bootstrap-5.0.2-dist/, font-awesome-4.7.0/
  tools/           mailbox.php (read captured demo mail; users see their own,
                   admins see all), mail_test.php (test the mail settings)
  wsym.sql         database schema + demo accounts
  .env.example     configuration template
```

`lib/` in one line each:

| File | What it holds |
|---|---|
| `env.php` | reads `.env` (no external library) |
| `db.php` | MySQLi connection + `db_rows / db_row / db_val / db_run` |
| `html.php` | page header/footer, escaping, the shared "action succeeded" screen |
| `auth.php` | sessions, login, `require_login()`, CSRF tokens, the permission checks |
| `money.php` | amount parsing and the three money movements, in one transaction |
| `accounts.php` | rename, change email, change password, force verify/reset |
| `mail.php` | the four transports; Gmail/SMTP go through PHPMailer |
| `phpmailer/src/` | PHPMailer 6.9.3 itself (`PHPMailer.php`, `SMTP.php`, `Exception.php`) |
| `tickets.php` | the conversation thread |
| `history.php` | the movement list and the CSV |
| `audit.php` | the administrator log |
| `icons.php` | the inline SVG icons of the admin sidebar (`icon_sprite()`, `icon()`) |
| `missing_images.php` | draws a stand-in if a `data/` picture is ever missing (no broken-image boxes) |

### The pictures in `data/`

Every image the design uses ships in the archive. Nothing is fetched from the internet.

| File | Where it shows |
|---|---|
| `background.jpg` | the page background on every screen (blurred, very light blue) |
| `favicon.ico` | the browser tab icon, the logo in both headers, the default profile picture |
| `logo.png` | the two cards on the about page |
| `user.svg`, `key.svg`, `mail.svg` | the little icons inside the form fields |
| `dollar.svg`, `repeat.svg` | the dashboard and password-reset icons |
| `stop.svg`, `write.svg`, `calender.svg`, `clock.svg` | the admin console icons |
| `uploads/` | profile pictures uploaded by users (must stay writable) |

You can replace any of them with a file of the same name. If one is ever deleted,
`lib/missing_images.php` draws the icon instead of showing a broken-image box, and
`check.php` lists the file as missing.

The background path is `../data/background.jpg` because the stylesheets live in `css/`.
If you change the folder layout, that `../` is the part to update.

---

## 6. How it is built (and why)

* **One action, one page.** Every form posts to the page that shows its result, so a
  refresh never repeats a bank transfer.
* **The amount shown is the amount moved.** The money functions return the value they
  actually wrote to the database; the result screen prints that value. Nothing is read
  back out of the session, so a receipt can never show `$0.00` for a real movement.
* **One result screen.** `show_result()` in `lib/html.php` draws every "Action Success /
  Action Failed" page, so all of them look and behave the same.
* **Passwords** are bcrypt hashes (`password_hash`), never plain text.
* **Every form carries a CSRF token**; every SQL statement is a prepared statement.
* **Isolation:** money movements run inside a transaction with `FOR UPDATE` on the
  balance row, so two clicks cannot spend the same money twice.
* **Errors are for humans.** `check.php` explains the fix; the result pages say what
  happened and what to do next.
* **The design fits a phone.** `css/phone.css` is loaded last on every page: the 700px
  cards become fluid, the top bar wraps and the console sidebar collapses into a
  "Console menu" button. No colour or font changes.

---

## 7. Deliberately not implemented

* No HTTPS setup, no rate limiting beyond the 60-second "resend email" cooldown.
* No in-app inbox except `tools/mailbox.php`, and that reader only has something to show
  when `mailer_transport=file` (section 3). With `smtp` or `gmail` the app hands the
  message over and keeps no copy, on purpose — read it in the destination mailbox, which
  is exactly what makes the local-relay setup private per person.
* Every user of one install shares the same temp folder, so it assumes one install =
  one business. Two banks on one server need two folders.
* `data/uploads/` must stay writable by the web server for profile pictures.
* A transparent PNG/GIF stays transparent after upload (the header shows the cut-out
  photo, not a white disc). A JPEG is re-saved as a JPEG, so it gets a white square.
* Without the GD extension the picture is stored as uploaded and cropped by CSS instead,
  which works the same — `check.php` reports it as information, not as a problem.
* `OLD_CODE/` holds the previous version of this project, kept only for reference; it is
  not used by anything and can be deleted.

---

## 8. Safety

This is a learning project, **not production-ready banking software**. Do not use real
money, real payment services, personal information or real credentials. The demo accounts
are public and disposable: assume anyone can log in with them.

Before any public deployment: change or remove the demo accounts, put a real database
password in `.env`, serve it over HTTPS, keep `.env` out of Git, and protect
`data/uploads/`.

---

## 9. Credits

* **PHPMailer 6.9.3** — <https://github.com/PHPMailer/PHPMailer> — LGPL-2.1, included
  unmodified in `lib/phpmailer/` (its licence file is next to it).
* **Bootstrap 5.0.2** and **Font Awesome 4.7** — included in the archive.

---

## 10. Author

**Mohamed Khabou**
Computer Science Student — Faculté des Sciences de Sfax (FSS), Sfax, Tunisia
[GitHub](https://github.com/Pixelmidou) · [LinkedIn](https://linkedin.com/in/mohamedkhabou)
