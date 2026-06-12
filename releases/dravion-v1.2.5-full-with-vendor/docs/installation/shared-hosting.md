# Installation — Shared Hosting

## Requirements
- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- mod_rewrite enabled
- Extensions: PDO, mbstring, openssl, tokenizer, xml, ctype, json, bcmath

## Steps

### 1. Upload files
Upload ZIP contents to your `public_html/` (or subdirectory).

### 2. Create database
cPanel → MySQL Databases:
- Create database (e.g. `mysite_dravion`)
- Create user + strong password
- Assign user to DB → All Privileges

### 3. Run installer
Open in browser:
```
https://yourdomain.com/install
```
Installer wizard will:
- Check requirements
- Configure database
- Run migrations
- Set admin account
- Activate license key
- Lock installer

### 4. Done
Installer deletes itself. Login at `/login`.
