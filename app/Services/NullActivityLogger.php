<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use Illuminate\Database\Eloquent\Model;

class NullActivityLogger implements ActivityLoggerInterface
{
    public function log(
        string $category,
        string $event,
        string $description,
        ?Model $subject = null,
        ?Model $causer = null,
        string $descKey = '',
        array $descParams = []
    ): void {
        // no-op — used in tests to avoid DB dependency
    }

    public function enabled(string $key): bool
    {
        return false;
    }
}
