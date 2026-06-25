<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GitHubZipUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $owner = config('updater.owner');
        $repo  = config('updater.repo');

        // Two valid URL shapes for this repo:
        //   zipball_url:  https://api.github.com/repos/{owner}/{repo}/zipball/...
        //   asset URL:    https://github.com/{owner}/{repo}/releases/download/...
        $allowed = [
            "https://api.github.com/repos/{$owner}/{$repo}/",
            "https://github.com/{$owner}/{$repo}/releases/download/",
        ];

        if (! is_string($value) || ! collect($allowed)->contains(fn ($p) => str_starts_with($value, $p))) {
            $fail('Invalid update source.');
        }
    }
}
