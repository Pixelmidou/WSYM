# WSYM Banking System

A fictional banking-system project built as a hands-on PHP/MySQL portfolio demo. WSYM ("We Steal Your Money") showcases account flows, simulated transactions, and user/admin interfaces.

> **Try it live:** [https://wsym.nysr.host/](https://wsym.nysr.host/index.php)  
> This is a simulation only—no real banking, money, payments, or financial services.

---

## Purpose

This project demonstrates a database-driven PHP application, including user authentication, session-based access, email verification, account management, and administrative workflows. Balances and transaction records are simulated in the demo database.

---

## Features

- User registration, login, and email-verification/resend flows
- Account settings, password changes, and profile-image management
- Simulated balance, deposit, withdrawal, and wire-transfer workflows
- Support-ticket workflow
- Admin interface for reviewing accounts and transactions, adjusting demo balances, managing access, and handling tickets
- Responsive pages with client-side JavaScript interactions

---

## Technologies

- **Backend:** PHP, MySQLi (object-oriented), MySQL/MariaDB
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5
- **Email & configuration:** PHPMailer, `vlucas/phpdotenv`

---

## Repository Structure

```text
WSYM/
├── index.php                      # Login, registration, and password-request interface
├── login.php / register.php       # Authentication and registration handlers
├── linking.php                    # Database connection and dotenv loading
├── mailer.php                     # SMTP / PHPMailer setup
├── welcome.php                    # User area
├── welcome_admin.php              # Admin area and transaction management
├── account_settings.php           # User account settings
├── email_verif_process.php        # Email-verification handler
├── resend_email_verif.php         # Resend verification email
├── uploadimg.php                  # Profile-image upload handling
├── wsym_hosted.sql                # Database schema and demo seed data
├── css/ and js/                   # Stylesheets and client-side scripts
├── bootstrap-5.0.2-dist/          # Local Bootstrap assets
└── phpmailer/src/                 # PHPMailer source files used by the app
```

---

## Local Setup

For local testing, install **XAMPP** (or an equivalent PHP + MySQL/MariaDB stack) and place the project in its web root. Make sure PHP's `mysqli` extension and `mysqlnd` are enabled, and that Composer, `vlucas/phpdotenv`, and the PHPMailer files under `phpmailer/src/` are available. Create a `.env` file in the project root with the database and SMTP settings expected by `linking.php` and `mailer.php`:

```dotenv
database_hostname=127.0.0.1
database_username=your_database_user
database_password=your_database_password
database_name=your_database_name

mailer_host=smtp.example.com
mailer_username=your_smtp_username
mailer_password=your_smtp_password
email_verif_address=WSYM Demo <no-reply@example.com>
```

The mailer expects authenticated SMTP over SSL on port `465`; email sending also depends on valid SMTP credentials and outbound SMTP access. Email templates contain hard-coded `localhost` URLs, so update those to match your local host and project path if testing verification links.

Import `wsym_hosted.sql` into a new, empty demo database using XAMPP's phpMyAdmin or another MySQL client. **The SQL file includes `DROP TABLE IF EXISTS` statements and sample data—never import it into a database containing data you need to keep.**

---

## Demo Safety

This is a fictional learning project, **not production-ready banking software**. Do not use real money, payment services, personal information, or real account credentials. The repository includes demo-account material; assume it is public and disposable. Keep `.env` out of Git. Before any public deployment, review the application's security, remove or change demo accounts, secure or remove `adminer.php`, configure HTTPS, and protect uploaded files.

---

## Author

**Mohamed Khabou**  
Computer Science Student — Faculté des Sciences de Sfax (FSS), Sfax, Tunisia  
[GitHub](https://github.com/Pixelmidou) · [LinkedIn](https://linkedin.com/in/mohamedkhabou)
