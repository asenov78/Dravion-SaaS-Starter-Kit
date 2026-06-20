<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ActivityLoggerInterface
{
    public function log(
        string $category,
        string $event,
        string $description,
        ?Model $subject = null,
        ?Model $causer = null,
        string $descKey = '',
        array $descParams = []
    ): void;

    public function enabled(string $key): bool;
}
