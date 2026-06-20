# app/Services/Updater

Sub-services extracted from `UpdaterService`. Each class has a single responsibility. `UpdaterService` is the thin orchestrator that composes them.

## Files

| File | Purpose |
|---|---|
| `ReleaseDownloader.php` | Downloads a GitHub release ZIP to a local path via Laravel HTTP client. Returns `{ok, path, message}`. Attaches `Authorization: Bearer {token}` header if `updater.token` is set. Timeout: 120s. |
| `ReleaseInstaller.php` | Extracts the ZIP, copies non-protected files over `base_path()`, detects version from extracted `config/dravion.php`, writes the new version back to the live config, runs `migrate --force`, clears caches, resets OPcache. Returns `{ok, message, version, changelog}`. |
| `UpdateHistory.php` | Reads and appends to `storage/app/updates/history.json`. Methods: `all()`, `append(from, to, changelog)`, `ensureExists(currentVersion)` (seeds an initial entry from `install.lock` date if history file is missing). |

## Flow (orchestrated by UpdaterService::install())

1. `ReleaseDownloader::download(zipUrl, destPath)` — fetch archive.
2. `ReleaseInstaller::install(zipPath, extractPath, changelog)` — extract and apply.
3. `UpdateHistory::append(fromVersion, toVersion, changelog)` — record the update.

## Protected paths

Never overwritten during `copyTree()` (configured in `config/updater.php`):
`.env`, `config/dravion.php`, `config/google2fa.php`, `storage`, `vendor`, `node_modules`, `public/storage`, `public/build`.

## Gotchas

- `ReleaseInstaller` deletes `storage/license.cache` after install — forces fresh license validation on next request.
- `ReleaseInstaller::locateExtractedRoot()` handles GitHub's convention of wrapping the archive in a single top-level directory.
- `ReleaseInstaller::copyTree()` path-traversal guards: skips any file whose resolved path doesn't start with `base_path()`.
- `UpdateHistory::ensureExists()` only seeds an initial entry if `install.lock` exists — prevents phantom history entries on fresh dev installs.
