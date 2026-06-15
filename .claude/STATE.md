# Session State — Dravion SaaS Starter Kit

> Updated: 2026-06-15 | Version: 1.3.1

## Current State

- **Tests:** 370/370 passing, 6 risky (acceptable)
- **Branch:** main, up to date with origin
- **Last commit:** `1d1473b` — feat: activity log export CSV + filter by all params

## Completed This Session

1. Test suite fixed — 356/356 → 370/370
2. Security audit: SSRF, HMAC cache, path traversal, rate limiting, session hardening
3. GitHub Actions SHA pinning
4. CLAUDE.md files generated in all major directories
5. AGENTS.md + docs/architecture.md + docs/security.md created
6. STATE.md system established
7. **#18** — Bulk user actions (suspend/activate/delete) with Alpine checkboxes
8. **#19** — Rename role (PUT route, inline Alpine edit, admin guard)
9. **#23** — Activity log filter UI (user, event type, date_from, date_to)
10. **#24** — Activity log export CSV (respects active filters)

## Pending / Next Steps (ordered)

- [ ] **#25** Settings — Global default language (settings form + locale middleware)
- [ ] **#20** Auth — Email verification (MustVerifyEmail + routes)
- [ ] **#22** Alert — Admin broadcast banner (site-wide, dismissible)
- [ ] **#26** Notifications — In-app bell + read/unread feed
- [ ] **#29** Users — Verify CSV export is linked in UI (✓ already in header)
- [ ] **#27** API — Laravel Sanctum tokens page
- [ ] **#28** Security — Session management (kill active sessions)
- [ ] **#21** Auth — 2FA / TOTP (complex, later)

## Architecture Snapshot

```
Laravel 13 / PHP 8.3 / Tailwind v4 / Alpine.js v3
Auth:     LoginController (manual, no Breeze)
License:  LicenseService → HMAC cache → license server (apsbg.com)
Updater:  UpdaterService → GitHub API → GitHubZipUrl rule → copyTree()
Roles:    Spatie (admin, manager, user, editor) — rename now supported
i18n:     lang/en/ + lang/bg/ — ALL strings via __()
Tests:    SQLite in-memory, Http::fake() for external calls
Bulk:     POST /admin/users/bulk (suspend/activate/delete)
Activity: Filters: log_name, causer_id, date_from, date_to, search + CSV export
```

## Standing Instructions (always active)

- `/caveman` + `/tdd` — active every prompt
- Multi-agent — use Explore/Plan/CSO per task type
- Push after every completed change
- End every task with "Готов съм!"
- All UI strings via `__()`, never hardcoded in Blade
- Update STATE.md at session end
