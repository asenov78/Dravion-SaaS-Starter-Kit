# Session State — Dravion SaaS Starter Kit

> Updated: 2026-06-15 | Version: 1.3.1

## Current State

- **Tests:** 409/409 passing, 6 risky (acceptable)
- **Branch:** main, up to date with origin
- **Last commit:** `536b3f1` — feat: Sanctum API tokens page

## Completed This Session

1-6. [earlier] security audit, CLAUDE.md files, STATE.md, bulk actions, rename role, activity log
7. **#25** — Global default language
8. **#22** — Admin broadcast banner
9. **#20** — Email verification (MustVerifyEmail + signed URL)
10. **#26** — In-app notification bell (JSON feed, mark read/all, Alpine.js dropdown)
11. **Notifications wired** — AccountSuspended/Activated → DB channel; NewUserRegistered + UpdateInstalled → admins
12. **#27** — Sanctum API tokens page (create/revoke/copy-once, lang en+bg)

## Pending / Next Steps (ordered)

- [ ] **#28** Security — Session management (kill active sessions)
- [ ] **#29** Users — Export CSV (verify linked in UI)
- [ ] **#21** Auth — 2FA / TOTP (complex, later)

## Architecture Snapshot

```\nLaravel 13 / PHP 8.3 / Tailwind v4 / Alpine.js v3 / Sanctum\nAuth:     LoginController (manual) + MustVerifyEmail + VerificationController\nSanctum:  HasApiTokens on User; GET/POST/DELETE /api-tokens\nBell:     NotificationController JSON feed; Alpine.js dropdown in app-header\nNotifs:   DB channel on suspend/activate (→user) + new-user/update (→admins)\nLicense:  LicenseService → HMAC cache → license server\nUpdater:  UpdaterService → GitHub API → copyTree()\nRoles:    Spatie (admin/manager/editor/user)\ni18n:     lang/en/ + lang/bg/ — 15 files including tokens.php + notifications.php\nTests:    SQLite in-memory, Http::fake() for external calls\n```\n
## Standing Instructions (always active)

- caveman + tdd — active every prompt
- Multi-agent — Explore/Plan/CSO per task
- Push after every completed change
- End every task with "Готов съм!"
- All UI strings via `__()`, never hardcoded in Blade
- Update ALL relevant CLAUDE.md files after every task
- Update STATE.md at session end
