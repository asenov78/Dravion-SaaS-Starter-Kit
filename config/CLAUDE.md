# config/

Laravel configuration files. All standard Laravel configs plus two project-specific ones.

## Project-specific files

### `dravion.php`
| Key | Env var | Purpose |
|---|---|---|
| `version` | — (hardcoded) | Current app version (e.g. `1.10.56`). Written by `ReleaseInstaller` on update. Read via `config('dravion.version')`. |
| `license_server` | `DRAVION_LICENSE_SERVER` | URL of the license validation server. |
| `updates_server` | `DRAVION_UPDATES_SERVER` | URL of the updates server. |
| `license_key` | `DRAVION_LICENSE_KEY` | Active license key. `DEV-*` prefix = dev mode (local domains only). |
| `licensed_domain` | `DRAVION_LICENSED_DOMAIN` | Domain the license was issued for. |

### `updater.php`
| Key | Env var | Purpose |
|---|---|---|
| `owner` | `GITHUB_OWNER` | GitHub org/user owning the releases repo. |
| `repo` | `GITHUB_REPO` | GitHub repo name. |
| `token` | `GITHUB_TOKEN` | Fine-grained PAT (`contents:read` scope, this repo only). Empty = public repo access. |
| `work_dir` | — | `storage/app/updates` — staging area for downloaded ZIPs. |
| `protected_paths` | — | Paths never overwritten by an update: `.env`, `config/dravion.php`, `config/google2fa.php`, `storage`, `vendor`, `node_modules`, `public/storage`. Note: `public/build` is NOT protected — the release ZIP includes compiled assets built by CI. |

## Standard Laravel configs (present, not customised)

`app.php`, `auth.php`, `cache.php`, `database.php`, `filesystems.php`, `logging.php`, `mail.php`, `permission.php` (Spatie), `queue.php`, `sanctum.php`, `services.php`, `session.php`

`google2fa.php` — 2FA config (in protected_paths — not overwritten by updater).

## Conventions

- `config/dravion.php` is in `updater.protected_paths` — never overwritten during auto-update. Only `ReleaseInstaller` patches the `version` key in-place.
- Never read `DRAVION_LICENSE_KEY` with `env()` directly — always use `config('dravion.license_key')` so it works after `config:cache`.
- Version bump on every behavioral commit: edit `config/dravion.php` `version` value and add entry to `CHANGELOG.md`.
