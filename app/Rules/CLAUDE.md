# app/Rules

Custom Laravel validation rules implementing `ValidationRule`. Each rule is single-purpose and reusable across controllers.

## Files

| File | Purpose |
|---|---|
| `GitHubZipUrl.php` | Validates that a ZIP URL belongs to the configured GitHub repo (`updater.owner` / `updater.repo`). Used in `UpdateController` to prevent installing arbitrary ZIPs. Checks prefix `https://api.github.com/repos/{owner}/{repo}/`. |

## Conventions

- Implement `Illuminate\Contracts\Validation\ValidationRule` (not the older `Rule` interface).
- Use the `validate(string $attribute, mixed $value, Closure $fail): void` signature.
- Call `$fail('message')` to signal failure — do not throw exceptions.
- Error messages passed to `$fail()` should be translatable: prefer `$fail(__('validation.custom.rule_name'))` or a dedicated lang key.
- One rule per file, named after what it validates (noun or noun-phrase), e.g. `UniqueSlug`, `ValidTimezone`.

## Gotchas

- `GitHubZipUrl` reads `config('updater.owner')` and `config('updater.repo')` at validation time — if these are empty, the prefix becomes `https://api.github.com/repos//` and no URL will ever pass. Ensure `config/updater.php` is populated.
- Rules in this directory are not auto-discovered — import and instantiate them explicitly in `$request->validate(['field' => [new GitHubZipUrl]])`.
