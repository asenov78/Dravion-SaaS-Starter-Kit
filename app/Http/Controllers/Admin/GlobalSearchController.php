<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class GlobalSearchController extends Controller
{
    private const MIN_LENGTH = 3;

    private const ALL_GROUPS = ['users', 'roles', 'activity', 'settings', 'languages'];

    public function search(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (mb_strlen($q) < self::MIN_LENGTH) {
            return response()->json(['results' => []]);
        }

        $groups = $request->input('groups', []);
        $groups = array_values(array_filter((array) $groups, fn ($g) => in_array($g, self::ALL_GROUPS)));
        if (empty($groups)) {
            $groups = self::ALL_GROUPS;
        }

        $searchers = [
            'users'     => fn () => $this->searchUsers($q),
            'roles'     => fn () => $this->searchRoles($q),
            'activity'  => fn () => $this->searchActivity($q),
            'settings'  => fn () => $this->searchSettings($q),
            'languages' => fn () => $this->searchLanguages($q),
        ];

        $results = [];
        foreach ($groups as $group) {
            if (isset($searchers[$group])) {
                $results = array_merge($results, ($searchers[$group])());
            }
        }

        return response()->json(['results' => $results]);
    }

    private function searchUsers(string $q): array
    {
        $matches = User::where(fn ($w) =>
            $w->where('name', 'like', "%{$q}%")
              ->orWhere('email', 'like', "%{$q}%")
        )
        ->limit(5)
        ->get();

        if ($matches->isEmpty()) {
            return [];
        }

        $listUrl = route('admin.users.index') . '?search=' . urlencode($q);

        return $matches->map(fn (User $u) => [
            'group' => 'users',
            'label' => $u->name,
            'meta'  => $u->email,
            'url'   => $listUrl,
        ])->all();
    }

    private function searchRoles(string $q): array
    {
        return Role::where('name', 'like', "%{$q}%")
            ->limit(5)
            ->get()
            ->map(fn (Role $r) => [
                'group' => 'roles',
                'label' => $r->name,
                'meta'  => null,
                'url'   => route('admin.roles.index'),
            ])
            ->all();
    }

    private function searchActivity(string $q): array
    {
        return Activity::where('description', 'like', "%{$q}%")
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Activity $a) => [
                'group' => 'activity',
                'label' => str($a->description)->limit(60)->toString(),
                'meta'  => $a->created_at?->diffForHumans(),
                'url'   => route('admin.activity') . '?search=' . urlencode($a->description),
            ])
            ->all();
    }

    private function searchSettings(string $q): array
    {
        return Setting::where('key', 'like', "%{$q}%")
            ->orWhere('value', 'like', "%{$q}%")
            ->limit(5)
            ->get()
            ->map(fn (Setting $s) => [
                'group' => 'settings',
                'label' => $s->key,
                'meta'  => str($s->value)->limit(40)->toString(),
                'url'   => route('admin.settings'),
            ])
            ->all();
    }

    private function searchLanguages(string $q): array
    {
        return Language::where('name', 'like', "%{$q}%")
            ->orWhere('code', 'like', "%{$q}%")
            ->limit(5)
            ->get()
            ->map(fn (Language $l) => [
                'group' => 'languages',
                'label' => $l->name,
                'meta'  => $l->code,
                'url'   => route('admin.languages.edit', $l->id),
            ])
            ->all();
    }

}
