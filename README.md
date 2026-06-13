# Nevo : managing routines for sick children and their parents

Nevo is a website that uses gamification to help parents manage daily routines for children with chronic conditions or neurodevelopmental disorders (ADHD, autism). Through quests, rewards, and progress tracking, children stay motivated to follow their routines (medication, sleep, hygiene, meals...), while parents and medical staff can monitor progress.

## Features

- **Parents**: create custom habits and routines, define rewards and quests, monitor child's progress via dashboard
- **Children**: complete quests and routines, collect rewards, express feelings (emojis/text), track streaks
- **Medical staff**: read children's feelings, track routines

## Tech Stack

- Frontend: HTML/CSS/JS (MVC architecture)
- Backend: PHP
- Database: PostgreSQL (hosted on Supabase)
- Testing: Jest (unit) & Playwright (end-to-end)
- Local environment: XAMPP

## Installation

### 1. Clone the repository

Clone into the XAMPP `htdocs` folder, here the command for windows:

```bash
cd C:\xampp\htdocs
git clone https://github.com/integeek/Nevo.git
```

Alternatively, you can keep the project elsewhere and update Apache's `httpd.conf` (`DocumentRoot` and `Directory` directives) to point to your project folder.

### 2. Configure the database

Nevo uses a PostgreSQL database hosted on Supabase:

The database details are not included in the repository for security reasons. Add it to `bdd.php` and `cleanup.php` to connect to the database.

In `php.ini` (Apache configuration in XAMPP), enable the following extensions:

```ini
extension=pdo_pgsql
extension=pgsql
```

### 3. Configure email sending

In `php.ini`, add the SMTP configuration:

```ini
[mail function]
SMTP=smtp.gmail.com
smtp_port=465
sendmail_from = <email>
sendmail_path = C:\xampp\sendmail\sendmail.exe
```

In the `sendmail.ini` file (located in the `sendmail` folder):

```ini
smtp_server = smtp.gmail.com
smtp_port = 465
auth_username = <email>
auth_password = <smtp_app_password>
```

### 4. Install dependencies

```bash
npm install
```

If needed:

```bash
npm install --save-dev @playwright/test
npx playwright install
npm install --save-dev jest
```

## Running the application

Preliminary checks:

- Apache is started from XAMPP
- MySQL is started from XAMPP
- The Nevo folder is in `htdocs` OR `httpd.conf` has been modified
- The database password has been entered in `bdd.php`
- The `php.ini` and `sendmail.ini` files have been configured

Then access the application at:

```
http://localhost/View/Page/homelogin.php
```

## Running the tests

Unit tests:

```bash
npm test
```

End-to-end tests:

```bash
npx playwright test
```

## Test accounts

| Role | Identifiers |
|---|---|
| Parent | parent@nevo.com / Nevo2026! |
| Medical staff | medical@nevo.com / Nevo2026! |
| Child | Louise and Nicolas / 1234 |
