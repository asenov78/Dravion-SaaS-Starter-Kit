<?php

namespace App\Observers;

use App\Models\User;
use App\Facades\ActivityLogger;

class UserObserver
{
    public function created(User $user): void
    {
        // Skip during seeding/install (no authenticated user)
        if (! auth()->check()) {
            return;
        }

        ActivityLogger::log(
            'users', 'created',
            "Created user {$user->name} ({$user->email})",
            $user, null,
            'activity.log.user_created',
            ['name' => $user->name, 'email' => $user->email]
        );
    }

    public function updated(User $user): void
    {
        if (! auth()->check()) {
            return;
        }

        // Only log meaningful field changes, not internal updates (email_verified_at, remember_token, etc.)
        $relevant = array_intersect(array_keys($user->getDirty()), [
            'name', 'email', 'status', 'bio', 'phone', 'country',
            'city_state', 'postal_code', 'tax_id', 'facebook', 'x_url', 'linkedin', 'instagram',
        ]);

        if (empty($relevant)) {
            return;
        }

        ActivityLogger::log(
            'users', 'updated',
            "Updated user {$user->name} ({$user->email})",
            $user, null,
            'activity.log.user_updated',
            ['name' => $user->name, 'email' => $user->email]
        );
    }

    public function deleted(User $user): void
    {
        if (! auth()->check()) {
            return;
        }

        ActivityLogger::log(
            'users', 'deleted',
            "Deleted user {$user->name} ({$user->email})",
            $user, null,
            'activity.log.user_deleted',
            ['name' => $user->name, 'email' => $user->email]
        );
    }

    public function restored(User $user): void
    {
        if (! auth()->check()) {
            return;
        }

        ActivityLogger::log(
            'users', 'restored',
            "Restored user {$user->name} ({$user->email})",
            $user, null,
            'activity.log.user_restored',
            ['name' => $user->name, 'email' => $user->email]
        );
    }
}

