# Session State — Dravion SaaS Starter Kit

> Updated: 2026-06-15 | Version: 1.3.1

## Current State

- **Tests:** 385/385 passing, 6 risky (acceptable)
- **Branch:** main, up to date with origin
- **Last commit:** `102d131` — feat: email verification (MustVerifyEmail + signed URL + resend)

## Completed This Session

1. Test suite fixed — 356/356 -> 370/370
2. Security audit: SSRF, HMAC cache, path traversal, rate limiting, session hardening
3. GitHub Actions SHA pinning
4. CLAUDE.md files generated in all major directories
5. AGENTS.md + docs/architecture.md + docs/security.md created
6. STATE.md system established
7. **#18** — Bulk user actions (suspend/activate/delete) with Alpine checkboxes
8. **#19** — Rename role (PUT route, inline Alpine edit, admin guard)
9. **#23** — Activity log filter UI (user, event type, date_from, date_to)
10. **#24** — Activity log export CSV (respects active filters)
11. **#25** — Settings: global default language
12. **#22** — Admin broadcast banner (site-wide, dismissible via sessionStorage)
13. **#20** — Email verification (MustVerifyEmail, signed URL verify, resend, verify notice page)

## Pending / Next Steps (ordered)

- [ ] **#26** Notifications — In-app bell + read/unread feed
- [ ] **#27** API — Laravel Sanctum tokens page
- [ ] **#28** Security — Session management (kill active sessions)
- [ ] **#29** Users — Export CSV (verify linked in UI)
- [ ] **#21** Auth — 2FA / TOTP (complex, later)

## Architecture Snapshot

```\nLaravel 13 / PHP 8.3 / Tailwind v4 / Alpine.js v3\nAuth:     LoginController (manual, no Breeze) + MustVerifyEmail + VerificationController\nLicense:  LicenseService -> HMAC cache -> license server (apsbg.com)\nUpdater:  UpdaterService -> GitHub API -> GitHubZipUrl rule -> copyTree()\nRoles:    Spatie (admin, manager, user, editor) — rename now supported\ni18n:     lang/en/ + lang/bg/ — ALL strings via __()\nTests:    SQLite in-memory, Http::fake() for external calls\nBulk:     POST /admin/users/bulk (suspend/activate/delete)\nActivity: Filters: log_name, causer_id, date_from, date_to, search + CSV export\nVerify:   GET /email/verify (notice), GET /email/verify/{id}/{hash} (signed), POST /email/verification-notification (resend)\nDashboard: requires auth + verified middleware\n```\n
## Standing Instructions (always active)

- `/caveman` + `/tdd` — active every prompt
- Multi-agent — use Explore/Plan/CSO per task type
- Push after every completed change
- End every task with "Готов съм!"
- All UI strings via `__()`, never hardcoded in Blade
- Update STATE.md at session end
