<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger implements ActivityLoggerInterface
{
    public function enabled(string $key): bool
    {
        return Setting::get('activity_log_' . $key, '1') === '1';
    }

    public function log(
        string $category,
        string $event,
        string $description,
        ?Model $subject = null,
        ?Model $causer = null,
        string $descKey = '',
        array $descParams = []
    ): void {
        if (! $this->enabled($category)) {
            return;
        }

        $causer ??= auth()->user();

        $props = ['description' => $description];
        if ($descKey) {
            $props['desc_key']    = $descKey;
            $props['desc_params'] = $descParams;
        }

        $builder = activity($category)
            ->event($event)
            ->withProperties($props);

        if ($causer) {
            $builder = $builder->causedBy($causer);
        }

        if ($subject) {
            $builder = $builder->performedOn($subject);
        }

        $builder->log($description);
    }

    /**
     * Static shortcut — delegates to the container binding so tests can
     * swap in NullActivityLogger without touching callers.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return app(ActivityLoggerInterface::class)->$method(...$args);
    }
}
