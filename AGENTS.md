# AGENTS.md — Dravion SaaS Starter Kit

Agent-type guide for Claude Code and multi-agent workflows on this project.

---

## Active Modes (always on)

### Caveman Mode
All responses compressed. Drop articles, filler, pleasantries. Full technical precision kept.
Invoke: implicit — always active on this project.

### TDD Mode
Test first, always. Red → Green → Refactor. No feature code without a failing test first.
Invoke: implicit — always active on this project.

---

## Agent Types

### Explore Agent
**Purpose:** Find files, locate symbols, understand codebase structure.
**Use when:**
- "Where is X implemented?"
- "Which controller handles Y?"
- "Find all uses of LicenseService"
- Navigating unfamiliar parts of the codebase

**Tools:** Glob, Grep, Read
**Do not:** Edit files. Only read and report.

---

### Plan Agent
**Purpose:** Architecture decisions before big features.
**Use when:**
- Starting a new admin section
- Deciding between approaches (e.g., queue vs sync, event vs observer)
- Designing a new service class or data model
- Any change touching 5+ files

**Output:** Decision summary, file list, migration plan, test plan. No code written.
**Skill:** `/engineering:architecture` for ADR-style decisions.

---

### Code Reviewer Agent
**Purpose:** Security audits, PR review, correctness check.
**Use when:**
- Before merging any feature touching auth, permissions, file uploads, or the updater
- Reviewing changes to `LicenseService`, `UpdaterService`, `InstallController`
- After any new route is added
- Suspecting N+1 queries or missing permission guards

**Skill:** `/code-review` or `/security-review`
**Focus areas:**
- OWASP Top 10 (injection, broken auth, insecure upload, IDOR)
- Permission checks at route + controller level
- Sensitive env vars not committed
- Fine-grained PAT scope (GitHub token)
- Install route not re-exposed after install

---

### General-Purpose Agent
**Purpose:** Multi-step research, cross-cutting tasks.
**Use when:**
- Debugging an issue that spans multiple layers (route → controller → service → view)
- Investigating a test failure with unclear root cause
- Researching how a Spatie package feature works

**No special invocation** — default Claude Code behavior.

---

## Workflow Rules

1. **Always push after change.** Commit + push without asking.
2. **Bump version** in `config/dravion.php` + `CHANGELOG.md` on every behavioral change.
3. **i18n required.** Every new UI string needs `lang/en/` and `lang/bg/` entries.
4. **Tests required.** Every feature and bug fix needs a test. `composer test` must be green.
5. **Inline styles.** No Tailwind utility classes — inline styles only (project convention).

---

## Key Files Quick Reference

| Area | Path |
|------|------|
| Version | `config/dravion.php` |
| Updater config | `config/updater.php` |
| License logic | `app/Services/LicenseService.php` |
| Updater logic | `app/Services/UpdaterService.php` |
| Admin controllers | `app/Http/Controllers/Admin/` |
| Routes | `routes/web.php` |
| UI components | `resources/views/components/ui/` |
| Lang files | `lang/en/`, `lang/bg/` |
| Tests | `tests/Feature/`, `tests/Unit/` |
| Changelog | `CHANGELOG.md` |
| Install wizard | `app/Http/Controllers/InstallController.php` |
