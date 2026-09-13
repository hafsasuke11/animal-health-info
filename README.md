# Animal Disease Information System

A simple web app for learning about common animal diseases and their treatments, with user accounts for registered visitors.

## Features

- Home, About, and Diseases pages with animal disease info
- User signup, login, and forgot-password flows
- MySQL-backed user accounts (hashed passwords)

## Tech stack

- PHP (mysqli / PDO) + MySQL
- HTML/CSS, no frontend framework

## Setup

1. Install a PHP + MySQL stack (e.g. [XAMPP](https://www.apachefriends.org/)).
2. Import the schema:
   ```
   mysql -u root < database.sql
   ```
3. Serve the project root with PHP's built-in server:
   ```
   php -S localhost:8000
   ```
4. Open `http://localhost:8000/index.html`.

Database credentials (`root` / no password, database `user_auth`) are set directly in `login.php`, `signup.php`, and `forgot_password.php` — update them there if your MySQL setup differs.

## Known limitations

- `login.php` does not currently verify credentials against the database — it accepts any non-empty email/password.
- `signup.php` uses the deprecated `FILTER_SANITIZE_STRING` constant (PHP 8.1+).
