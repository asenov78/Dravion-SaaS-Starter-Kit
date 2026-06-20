<?php

namespace App\Facades;

use App\Contracts\ActivityLoggerInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void log(string $category, string $event, string $description, ?\Illuminate\Database\Eloquent\Model $subject = null, ?\Illuminate\Database\Eloquent\Model $causer = null, string $descKey = '', array $descParams = [])
 * @method static bool enabled(string $key)
 *
 * @see \App\Services\ActivityLogger
 */
class ActivityLogger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ActivityLoggerInterface::class;
    }
}
