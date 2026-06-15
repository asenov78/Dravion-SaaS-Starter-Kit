# Installation — Shared Hosting

## Requirements

- PHP 8.3+
- MySQL 5.7+ / MariaDB 10.3+
- mod_rewrite enabled
- Extensions: PDO, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, gd (avatar resize)
- `allow_url_fopen` on (updater fetches GitHub releases)

## Steps

### 1. Upload files

Upload ZIP contents to `public_html/` (or subdirectory). The ZIP is structured so `public/` maps to `public_html/` on shared hosts.

### 2. Create database

cPanel → MySQL Databases:
- Create database (e.g. `mysite_dravion`)
- Create user + strong password
- Assign user to DB → All Privileges

### 3. Configure environment

Copy `.env.example` to `.env`. Set at minimum:

```
APP_URL=https://yourdomain.com
DB_HOST=localhost
DB_DATABASE=mysite_dravion
DB_USERNAME=mysite_user
DB_PASSWORD=strongpassword

DRAVION_LICENSE_KEY=your-license-key
DRAVION_LICENSED_DOMAIN=yourdomain.com
```

### 4. Run installer wizard

Open in browser:
```
https://yourdomain.com/install
```

The installer wizard will:
1. Check PHP/extension requirements
2. Test database connection
3. Run migrations (creates all tables including `settings`, `permissions`, `activity_log`)
4. Create admin account (name, email, password)
5. Activate license key against the license server
6. Generate `APP_KEY`
7. Lock and self-delete the installer

### 5. Done

Installer locks itself. Login at `/login`.

---

## Post-Install

### SMTP Mail

Admin → Settings → Mail. Configure SMTP credentials and use the "Test Email" button to verify delivery. Welcome emails and account notifications require a working mail config.

### Logo & Branding

Admin → Settings → Upload logo. Displayed in sidebar and emails.

### Self-Updater

Admin → Updates. Requires:
- Valid license key (gated)
- `GITHUB_TOKEN` env var (fine-grained PAT, `contents: read` scope, this repo only) — optional on public repo but required for private

Protected paths never overwritten by updates: `.env`, `storage/`, `vendor/`, `node_modules/`, `public/storage/`, `public/build/`.

---

## Troubleshooting

| Symptom | Fix |
|---------|-----|
| Blank page / 500 | Check `storage/logs/laravel.log`. Usually missing `APP_KEY` or wrong DB creds. |
| "GD not found" | Enable `php_gd2` extension in cPanel PHP config. |
| Installer already ran | If locked by mistake, delete `storage/app/installed` lock file. |
| Updates page — "No valid license" | Check `DRAVION_LICENSE_KEY` and `DRAVION_LICENSED_DOMAIN` in `.env`. |
| Avatar upload fails | Check `storage/app/public/avatars/` is writable. Run `php artisan storage:link`. |
