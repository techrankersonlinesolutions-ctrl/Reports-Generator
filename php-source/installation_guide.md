# Shared Hosting (cPanel) Deployment Guide

This guide details steps to set up the **Eagle Reports Generator** on shared hosting setups.

## Requirements
- Apache web server with `mod_rewrite` enabled (cPanel default).
- PHP 8.0+ with extensions: `PDO`, `pdo_mysql`, `gd`, `fileinfo`, `mbstring`.
- MySQL 5.7+ or MariaDB 10.3+.
- Composer (for installing mPDF on the hosting).

---

## 1. Uploading Files
1. Export the project folders as a single ZIP file.
2. In your **cPanel File Manager**, navigate to your public directory (usually `public_html`).
3. Upload the ZIP file and extract it.
4. Ensure files are structured cleanly in the target folder:
   ```
   public_html/
   ├── config/
   ├── controllers/
   ├── database/
   ├── models/
   ├── views/
   ├── uploads/   (Ensure write permissions: 755 or 777)
   ├── pdf/       (Ensure write permissions: 755 or 777)
   ├── index.php
   └── composer.json
   ```

---

## 2. Database Provisioning
1. Open **cPanel MySQL Database Wizard**.
2. Create a database named `eagle_reports_db` (or custom name).
3. Create a database user and assign a secure password.
4. Grant the user **ALL PRIVILEGES** to the new database.
5. In **phpMyAdmin**, select your newly created database and click the **Import** tab.
6. Upload the `/database/database.sql` file and click **Go**. This sets up the structural schemas and initial data.

---

## 3. Configuration Setup
1. Open `config/database.php` in the cPanel code editor.
2. Replace credentials to point to your new database:
   ```php
   private $host = "localhost"; // Usually localhost
   private $db_name = "your_cpanel_db_name";
   private $username = "your_cpanel_db_user";
   private $password = "your_cpanel_secure_password";
   ```
3. Open `config/config.php` in the editor.
4. Modify the `BASE_URL` to match your domain address:
   ```php
   define('BASE_URL', 'https://yourdomain.com'); // No trailing slash
   ```

---

## 4. Install PDF Package (mPDF)
1. Open a **Terminal** window in cPanel (or connect via SSH).
2. Navigate to your project folder:
   ```bash
   cd public_html/
   ```
3. Run the dependency installation:
   ```bash
   composer install --no-dev
   ```
This installs the `vendor/` directory and configures autoload paths.

---

## 5. Security & Verification
1. Access `https://yourdomain.com` in your browser.
2. Login using the default internal staff credentials:
   - **Email:** `admin@eagle.com`
   - **Password:** `password123`
3. Click on the **Profile** tab in the top bar to update your name, email, and set a new, secure password.
4. Click on **Settings** to modify global default presets (margins, theme colors).
