# Dravion SaaS — System Plan

Legend: ✓ done  ○ todo

---

## 1. Users ✓ DONE
- ✓ List + paginate
- ✓ Create user
- ✓ Edit all fields (profile + social + address)
- ✓ Suspend / Activate
- ✓ Assign role
- ○ Delete (soft) + restore
- ○ Bulk actions (suspend / delete)
- ○ Filter / search by role, status
- ○ Export CSV

---

## 2. Roles & Permissions ○ TODO
- ✓ Roles seeded (admin / manager / editor / user)
- ✓ Role assigned on user create / edit
- ○ Roles page — list all roles
- ○ Create / rename / delete role
- ○ Permissions matrix (role × permission checkboxes)
- ○ Permission groups (users, settings, …)
- ○ Guard middleware per permission

---

## 3. Auth & Profile ~ PARTIAL
- ✓ Login / Logout
- ✓ Register
- ✓ Profile page (bio, social, address)
- ○ Change password from profile
- ○ Avatar upload
- ○ Forgot password / reset
- ○ Email verify
- ○ 2FA (TOTP)

---

## 4. Alert System ✓ DONE
- ✓ x-ui.alert component (4 variants: success / error / warning / info)
- ✓ Flash alerts on profile / user edit
- ✓ Flash alerts on settings save
- ○ Toast / auto-dismiss JS notifications
- ○ Admin broadcast banner (site-wide)
- ○ Dismissible per-user
- ○ Validation inline errors on form fields

---

## 5. Activity Log ✓ DONE
- ✓ Log: login / logout
- ✓ Log: user create / update / suspend / activate
- ✓ Log: profile update
- ✓ Log: settings change
- ✓ Settings — toggle per category (auth / users / profile / settings)
- ○ Filter by user / event type / date range
- ○ Export log (CSV)

---

## 6. License ✓ DONE
- ✓ License key input + validate
- ✓ Remote license server (activate)
- ✓ Weekly ping / check
- ✓ Warning banner when invalid
- ✓ Admin license page
- ○ Grace period (offline X days)
- ○ License tier / feature flags

---

## 7. System Settings ~ PARTIAL
- ✓ App name, URL
- ✓ Mail from / name
- ✓ Registration on/off
- ✓ Activity log toggles
- ○ Timezone (PHP + JS display)
- ○ Default language
- ○ Date format / currency symbol
- ○ Logo / favicon upload
- ○ Maintenance mode toggle
- ○ SMTP test send

---

## 8. Language Manager ○ TODO
- ○ Languages list (code, name, flag emoji)
- ○ Add language
- ○ Set default language
- ○ Phrase editor — table of all keys, inline edit per lang
- ○ Fallback to EN if key missing
- ○ Import / export JSON
- ○ User language preference (stored on profile)
- ○ Middleware: set locale per request

---

## 9. Installer ✓ DONE
- ✓ Step wizard (requirements check)
- ✓ DB config + migrate
- ✓ Admin account create
- ✓ License key step
- ✓ install.lock guard
- ○ Re-install / reset wizard
- ○ Update wizard (run new migrations only)

---

## 10. Dashboard ~ PARTIAL
- ✓ Basic metrics cards
- ✓ Charts (ApexCharts)
- ○ Real KPIs (users count, active, suspended, …)
- ○ Recent activity widget
- ○ Quick actions panel
- ○ System health (disk usage, cache, queue)
- ○ License status widget

---

## 11. Notifications & Mail ○ TODO
- ○ In-app notification bell
- ○ Mark read / mark all read
- ○ Email on: new user registered, password reset
- ○ Email templates (Blade mailable)
- ○ SMTP settings + test send button
- ○ Queue driver config

---

## 12. API & Security ○ TODO
- ○ REST API (Laravel Sanctum tokens)
- ○ API tokens page (create / revoke)
- ○ Rate limiting config
- ○ Login attempt throttle
- ○ Blocked IPs list
- ○ Session management (kill active sessions)

---

## Suggested Build Order

### NEXT — high impact, natural fit
1. **Roles & Permissions page** — matrix UI (checkboxes per role × permission). Spatie middleware already in place.
2. **System Settings** — timezone, default lang, date format, logo upload, maintenance mode.
3. **Language Manager** — DB-backed translations table, phrase editor UI, locale middleware.

### THEN — completes the core
4. Change password from profile + forgot password / reset flow.
5. Dashboard real KPIs + recent activity widget.
6. In-app notifications bell + email on key events (welcome, reset).

### LATER — enterprise / SaaS extras
7. API tokens (Sanctum)
8. 2FA (TOTP)
9. Bulk user actions
10. CSV export (users + activity log)
11. Avatar upload
12. Login throttle / blocked IPs
