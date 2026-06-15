<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GitHubZipUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $owner  = config('updater.owner');
        $repo   = config('updater.repo');
        $prefix = "https://api.github.com/repos/{$owner}/{$repo}/";

        if (! is_string($value) || ! str_starts_with($value, $prefix)) {
            $fail('Invalid update source.');
        }
    }
}
