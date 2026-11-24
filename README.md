# WSYM — A Banking System Simulator

A fictional banking system, built as a hands-on PHP/MySQL portfolio project. WSYM shows
account flows, simulated transactions and both the customer and the administrator side of
a bank.

> **Try it live:** <https://wsym.nysr.host/index.php>
>
> **This is a simulation.** No real banking, no real money, no payments, no financial
> services. Balances and transactions exist only in the demo database.

* Plain **PHP 8 + MySQLi**, no framework, no Composer, no Python, no shell scripts.
* **Bootstrap 5.0.2** and **Font Awesome 4.7** ship in the folder, so it works offline.
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

## 3. Email

The demo works offline: with `mailer_transport=file` every message is written to a temp
folder and can be read at **`tools/mailbox.php`** (verification and password-reset links
included).

To send real mail from your own Gmail account:

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

4. Open **`tools/mail_test.php`**, type a recipient, press *Send*: it either reports that
   the mail was accepted or shows the SMTP error (wrong password, blocked port…).
   `check.php` also tells you which transport is active.

Any other server works too: `mailer_transport=smtp` with `mailer_host`, `mailer_port` and
`mailer_secure` (`tls` for port 587). The SMTP client is ~150 lines of `lib/mail.php`, so
there is no PHPMailer to install.

---

## 4. The `.env` file

```dotenv
database_hostname=localhost
database_username=root
database_password=
database_name=wsym

mailer_transport=file          # file | gmail | smtp | mail
mailer_username=
mailer_password=
mailer_port=465
mailer_secure=ssl

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
  css/ js/ data/   the design: stylesheets (phone.css = narrow screens), scripts, images
  bootstrap-5.0.2-dist/, font-awesome-4.7.0/
  tools/           mailbox.php (read demo mail), mail_test.php (test Gmail)
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
| `mail.php` | one SMTP client (Gmail, other SMTP, PHP mail, offline file) |
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
* No email inbox for the "file" transport other than `tools/mailbox.php`.
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

## 9. Author

**Mohamed Khabou**
Computer Science Student — Faculté des Sciences de Sfax (FSS), Sfax, Tunisia
[GitHub](https://github.com/Pixelmidou) · [LinkedIn](https://linkedin.com/in/mohamedkhabou)
