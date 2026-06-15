# Dravion SaaS — Plan
✓=done ~=partial ○=todo

## 1. Users ✓
✓ List/paginate ✓ Create ✓ Edit all fields ✓ Suspend/Activate ✓ Assign role
○ Delete(soft)+restore ○ Bulk actions ○ Filter/search ○ Export CSV

## 2. Roles & Permissions ○
✓ Roles seeded(admin/manager/editor/user) ✓ Role on create/edit
○ Roles page ○ Create/rename/delete role ○ Permissions matrix(role×permission checkboxes) ○ Permission groups ○ Guard middleware

## 3. Auth & Profile ~
✓ Login/Logout ✓ Register ✓ Profile(bio,social,address)
○ Change password ○ Avatar upload ○ Forgot/reset password ○ Email verify ○ 2FA(TOTP)

## 4. Alert System ✓
✓ x-ui.alert(4 variants) ✓ Flash on profile/user edit ✓ Flash on settings
○ Toast/auto-dismiss ○ Broadcast banner ○ Dismissible per-user ○ Inline validation errors

## 5. Activity Log ✓
✓ Log: login/logout ✓ Log: user create/update/suspend/activate ✓ Log: profile update ✓ Log: settings change ✓ Toggle per category
○ Filter by user/event/date ○ Export CSV

## 6. License ✓
✓ Key input+validate ✓ Remote server(activate) ✓ Weekly ping ✓ Warning banner ✓ Admin page
○ Grace period(offline X days) ○ License tier/feature flags

## 7. System Settings ~
✓ App name/URL ✓ Mail from/name ✓ Registration on/off ✓ Activity log toggles
○ Timezone ○ Default language ○ Date format/currency ○ Logo/favicon upload ○ Maintenance mode ○ SMTP test send

## 8. Language Manager ○
○ Languages list(code,name,flag) ○ Add language ○ Set default ○ Phrase editor(inline edit per lang) ○ Fallback to EN ○ Import/export JSON ○ User lang preference ○ Locale middleware

## 9. Installer ✓
✓ Step wizard ✓ DB config+migrate ✓ Admin create ✓ License step ✓ install.lock
○ Re-install wizard ○ Update wizard

## 10. Dashboard ~
✓ Metrics cards ✓ Charts(ApexCharts)
○ Real KPIs ○ Recent activity widget ○ Quick actions ○ System health ○ License widget

## 11. Notifications & Mail ○
○ In-app bell ○ Mark read ○ Email: new user/reset ○ Blade templates ○ SMTP test send ○ Queue config

## 12. API & Security ○
○ REST API(Sanctum) ○ API tokens page ○ Rate limiting ○ Login throttle ○ Blocked IPs ○ Kill sessions

## Build Order
NEXT: 1.Roles+Permissions matrix 2.System Settings(tz,lang,logo,maintenance) 3.Language Manager
THEN: 4.Change password+forgot/reset 5.Dashboard real KPIs 6.Notifications+email
LATER: 7.API tokens 8.2FA 9.Bulk actions 10.CSV export 11.Avatar 12.Login throttle/IPs
