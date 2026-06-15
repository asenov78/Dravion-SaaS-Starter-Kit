# Session State — Dravion SaaS Starter Kit

> Updated: 2026-06-15 | Version: 1.3.1

## Current State

- **Tests:** 356/356 passing, 6 risky (acceptable)
- **Branch:** main, up to date with origin
- **Last commit:** `38b32d8` — docs: CLAUDE.md per directory + AGENTS.md + architecture/security docs

## What Was Done This Session

1. Fixed 3 pre-existing test failures:
   - `test_user_can_logout` → assertRedirect `/login` (not `/`)
   - `ExampleTest` → assertStatus 302 (/ requires auth)
   - `InstallTest license` → curl → Http facade + `Http::fake()`
2. GitHub Actions SHA pinning (`release.yml`) — supply chain hardening
3. Full security audit + fixes:
   - SSRF protection via `GitHubZipUrl` rule
   - HMAC-signed `license.cache` (tamper-proof)
   - Pessimistic `LicenseService::isValid()` (no cache = false)
   - Rate limiting on all auth routes
   - No plain password in welcome email → password reset link
   - Suspended check before session creation
   - Session hardening (`SESSION_ENCRYPT`, `SESSION_SAME_SITE`)
4. Updates page: 2-column layout, changelogs, installed versions accordion
5. Generated CLAUDE.md in every major directory + AGENTS.md + docs/

## Pending / Next Steps

- [ ] Configure `GITHUB_TOKEN` in `.env` — generate new fine-grained PAT (old one was exposed in chat, must rotate)
  - Repository: `Dravion-SaaS-Starter-Kit` only
  - Permission: `Contents: Read-only`
  - Add to `.env`: `GITHUB_TOKEN=<new_token>`
- [ ] ionCube obfuscation build script (optional, deferred)
- [ ] Consider `SESSION_SECURE_COOKIE=true` check in tests

## Architecture Snapshot

```
Laravel 13 / PHP 8.3 / Tailwind v4 / Alpine.js v3
Auth:     LoginController (manual, no Breeze)
License:  LicenseService → HMAC cache → license server (apsbg.com)
Updater:  UpdaterService → GitHub API → GitHubZipUrl rule → copyTree()
Roles:    Spatie (admin, manager, user)
i18n:     lang/en/ + lang/bg/ — ALL strings via __()
Tests:    SQLite in-memory, Http::fake() for external calls
```

## Standing Instructions (always active)

- `/caveman` — terse responses, no filler
- `/tdd` — test first, vertical slices
- Multi-agent — use Explore/Plan/CSO agents per task type
- Push after every completed change, no confirmation needed
- End every task with "Готов съм!"
- All UI strings via `__()`, never hardcoded in Blade
