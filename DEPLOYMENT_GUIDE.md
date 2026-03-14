# Hostinger Deployment Guide

This guide will help you deploy the APS Advertising Admin Panel to Hostinger.

## 1. Prepare Files

Ensure you have the following folder structure ready to upload:

```
aps-advertising/
├── backend/          (Contains API logic and config)
├── public/           (Contains the frontend admin panel)
├── .htaccess         (Routing rules)
└── database.sql      (Database schema)
```

## 2. Upload to Hostinger

1.  Log in to Hostinger hPanel -> **File Manager**.
2.  Navigate to `public_html`.
3.  **Ideally**, create a subfolder (e.g., `admin-panel`) or upload the contents of the `aps-advertising` folder directly into `public_html` if this is the only site.
    - **Option A (Recommended for Subfolder):** Upload the entire `aps-advertising` folder into `public_html`. Access via `yourdomain.com/aps-advertising/public/admin`.
    - **Option B (Root Domain):** Upload the CONTENTS of `aps-advertising` into `public_html`. Access via `yourdomain.com/public/admin`.

## 3. Configure Database

1.  In hPanel, go to **Databases** -> **Management**.
2.  Create a new MySQL Database.
    - Note the **Database Name**, **Username**, and **Password**.
3.  Enter phpMyAdmin and import `database.sql`.
4.  **Edit Configuration:**
    - Open `backend/config/constants.php` in File Manager.
    - Update the database constants with your Hostinger credentials:
      ```php
      define('DB_HOST', 'localhost'); // Usually localhost on Hostinger
      define('DB_USER', 'u123456789_your_username');
      define('DB_PASS', 'your_password');
      define('DB_NAME', 'u123456789_your_db_name');
      ```

## 4. Verify Routing

1.  The project now includes a **Smart Router** in `backend/api/index.php`. It automatically detects if you are in a subfolder.
2.  The frontend (`public/admin/index.html`) also automatically detects the API URL.
3.  **Important:** Ensure the `.htaccess` file is present in your root folder (where `backend` and `public` are).

## 5. Troubleshooting Login

If you see "Server Login Failed":

1.  Open Developer Tools (F12) -> **Network** tab.
2.  Try logging in.
3.  Click the failed request (usually `user` or `login`).
4.  Check the **Response** tab. It should contain a JSON error message.
5.  If you see a 404, check that `backend/api/auth.php` exists and `.htaccess` is correct.
6.  If you see a 500, check `backend/config/constants.php` database credentials.

## 6. Security Note

On a live server, ensure `display_errors` is set to `0` in `backend/config/constants.php` (already configured by default in this update).
