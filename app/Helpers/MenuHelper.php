<?php

namespace App\Helpers;

use App\Services\UpdaterService;
use Illuminate\Support\Facades\Cache;

class MenuHelper
{
    public static function getMenuGroups(): array
    {
        return [
            [
                'title' => __('nav.main'),
                'items' => self::getMainItems(),
            ],
            [
                'title' => __('nav.system'),
                'items' => self::getSystemItems(),
            ],
            [
                'title' => 'UI Elements',
                'items' => self::getUiItems(),
            ],
        ];
    }

    public static function getUiItems(): array
    {
        return [
            [
                'icon' => 'ui',
                'name' => 'eCommerce',
                'path' => route('admin.ui.ecommerce'),
                'route' => 'admin.ui.ecommerce',
            ],
            [
                'icon' => 'ui',
                'name' => 'Components',
                'subItems' => [
                    ['name' => 'Alerts',  'path' => route('admin.ui.alerts')],
                    ['name' => 'Avatars', 'path' => route('admin.ui.avatars')],
                    ['name' => 'Badges',  'path' => route('admin.ui.badges')],
                    ['name' => 'Buttons', 'path' => route('admin.ui.buttons')],
                    ['name' => 'Images',  'path' => route('admin.ui.images')],
                    ['name' => 'Videos',  'path' => route('admin.ui.videos')],
                ],
            ],
            [
                'icon' => 'ui',
                'name' => 'Forms',
                'path' => route('admin.ui.form-elements'),
                'route' => 'admin.ui.form-elements',
            ],
            [
                'icon' => 'ui',
                'name' => 'Tables',
                'path' => route('admin.ui.tables'),
                'route' => 'admin.ui.tables',
            ],
            [
                'icon' => 'ui',
                'name' => 'Charts',
                'subItems' => [
                    ['name' => 'Bar Chart',  'path' => route('admin.ui.bar-chart')],
                    ['name' => 'Line Chart', 'path' => route('admin.ui.line-chart')],
                ],
            ],
            [
                'icon' => 'ui',
                'name' => 'Calendar',
                'path' => route('admin.ui.calendar'),
                'route' => 'admin.ui.calendar',
            ],
            [
                'icon' => 'ui',
                'name' => 'Profile',
                'path' => route('admin.ui.profile'),
                'route' => 'admin.ui.profile',
            ],
            [
                'icon' => 'ui',
                'name' => 'Blank Page',
                'path' => route('admin.ui.blank'),
                'route' => 'admin.ui.blank',
            ],
        ];
    }

    public static function getMainItems(): array
    {
        return [
            [
                'icon'  => 'dashboard',
                'name'  => __('nav.dashboard'),
                'path'  => route('admin.dashboard'),
                'route' => 'admin.dashboard',
            ],
            [
                'icon'  => 'users',
                'name'  => __('nav.users'),
                'path'  => route('admin.users.index'),
                'route' => 'admin.users.*',
            ],
            [
                'icon'  => 'roles',
                'name'  => __('nav.roles'),
                'path'  => route('admin.roles.index'),
                'route' => 'admin.roles.*',
            ],
            [
                'icon'  => 'pages',
                'name'  => __('nav.pages'),
                'path'  => route('admin.pages.index'),
                'route' => 'admin.pages.*',
            ],
            [
                'icon'  => 'custom-data',
                'name'  => __('nav.custom_data'),
                'path'  => route('admin.custom-data.index'),
                'route' => 'admin.custom-data.*',
            ],
            [
                'icon'  => 'activity',
                'name'  => __('nav.activity'),
                'path'  => route('admin.activity'),
                'route' => 'admin.activity',
            ],
        ];
    }

    public static function getSystemItems(): array
    {
        $items = [
            [
                'icon'  => 'settings',
                'name'  => __('nav.settings'),
                'path'  => route('admin.settings'),
                'route' => 'admin.settings',
            ],
            [
                'icon'  => 'languages',
                'name'  => __('nav.languages'),
                'path'  => route('admin.languages.index'),
                'route' => 'admin.languages.*',
            ],
        ];

        // Updates are admin-only (route guarded by role:admin).
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            $item = [
                'icon'  => 'updates',
                'name'  => __('nav.updates'),
                'path'  => route('admin.updates'),
                'route' => 'admin.updates',
            ];

            $current = ltrim(config('dravion.version', '0.0.0'), 'v');
            $latest  = self::resolveLatestVersion();
            if ($latest && version_compare(ltrim($latest, 'v'), $current, '>')) {
                $item['update_available'] = true;
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * Return the latest GitHub release version string, using the cache as the
     * primary source. If the cache is empty (no scheduler/webhook yet), attempt
     * a live fetch and write the result so subsequent requests are instant.
     * Failure is silent — no badge is better than a slow/broken sidebar.
     */
    private static function resolveLatestVersion(): ?string
    {
        $cached = Cache::get('github_latest_version');
        if ($cached !== null) {
            return $cached;
        }

        // Guard: skip if GitHub repo not configured or a recent fetch already failed.
        if (!config('updater.owner') || !config('updater.repo')) {
            return null;
        }
        if (Cache::get('github_check_failed')) {
            return null;
        }

        try {
            $release = app(UpdaterService::class)->getLatestRelease();
            if ($release) {
                Cache::put('github_latest_version', $release['version'], now()->addHours(4));
                return $release['version'];
            }
        } catch (\Throwable) {
            // GitHub unreachable — suppress badge, retry in 5 minutes
        }

        Cache::put('github_check_failed', true, now()->addMinutes(5));
        return null;
    }

    public static function isActive(string $routePattern): bool
    {
        return request()->routeIs($routePattern);
    }

    public static function getIconSvg(string $iconName): string
    {
        $icons = [
            'dashboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z" fill="currentColor"/></svg>',

            'roles' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9 2.75C7.20508 2.75 5.75 4.20507 5.75 6C5.75 7.79493 7.20508 9.25 9 9.25C10.7949 9.25 12.25 7.79493 12.25 6C12.25 4.20507 10.7949 2.75 9 2.75ZM4.25 6C4.25 3.37665 6.37665 1.25 9 1.25C11.6234 1.25 13.75 3.37665 13.75 6C13.75 8.62335 11.6234 10.75 9 10.75C6.37665 10.75 4.25 8.62335 4.25 6ZM15.75 4.25C15.3358 4.25 15 4.58579 15 5C15 5.41421 15.3358 5.75 15.75 5.75C16.7165 5.75 17.5 6.5335 17.5 7.5C17.5 8.4665 16.7165 9.25 15.75 9.25C15.3358 9.25 15 9.58579 15 10C15 10.4142 15.3358 10.75 15.75 10.75C17.5449 10.75 19 9.29493 19 7.5C19 5.70507 17.5449 4.25 15.75 4.25ZM2.5 17C2.5 14.5147 5.14573 12.75 9 12.75C12.8543 12.75 15.5 14.5147 15.5 17V18.25H2.5V17ZM1 17C1 13.5621 4.25432 11.25 9 11.25C13.7457 11.25 17 13.5621 17 17V19C17 19.4142 16.6642 19.75 16.25 19.75H1.75C1.33579 19.75 1 19.4142 1 19V17ZM17.5 12.75C17.0858 12.75 16.75 13.0858 16.75 13.5C16.75 13.9142 17.0858 14.25 17.5 14.25C19.2949 14.25 20.5 15.4353 20.5 17V18.25H18.25C17.8358 18.25 17.5 18.5858 17.5 19C17.5 19.4142 17.8358 19.75 18.25 19.75H21.25C21.6642 19.75 22 19.4142 22 19V17C22 13.9621 19.7543 12.75 17.5 12.75Z" fill="currentColor"/></svg>',

            'users' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.5C10.0617 3.5 8.5 5.06168 8.5 6.99998C8.5 8.93829 10.0617 10.5 12 10.5C13.9383 10.5 15.5 8.93829 15.5 6.99998C15.5 5.06168 13.9383 3.5 12 3.5ZM7 6.99998C7 4.23327 9.23328 2 12 2C14.7667 2 17 4.23327 17 6.99998C17 9.7667 14.7667 12 12 12C9.23328 12 7 9.7667 7 6.99998ZM5.5 20.5H18.5C18.2239 18.1333 16.3267 16.5 12 16.5C7.67328 16.5 5.77606 18.1333 5.5 20.5ZM4 21C4 17.134 7.13401 15 12 15C16.866 15 20 17.134 20 21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21Z" fill="currentColor"/></svg>',

            'activity' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M2.75 12C2.75 6.89137 6.89137 2.75 12 2.75C17.1086 2.75 21.25 6.89137 21.25 12C21.25 17.1086 17.1086 21.25 12 21.25C6.89137 21.25 2.75 17.1086 2.75 12ZM12 1.25C6.06294 1.25 1.25 6.06294 1.25 12C1.25 17.9371 6.06294 22.75 12 22.75C17.9371 22.75 22.75 17.9371 22.75 12C22.75 6.06294 17.9371 1.25 12 1.25ZM12.75 7C12.75 6.58579 12.4142 6.25 12 6.25C11.5858 6.25 11.25 6.58579 11.25 7V12C11.25 12.2652 11.3817 12.5136 11.6 12.66L15.1 15.16C15.4314 15.3948 15.8943 15.3157 16.129 14.9843C16.3638 14.6528 16.2847 14.1899 15.9533 13.9552L12.75 11.7V7Z" fill="currentColor"/></svg>',

            'settings' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.4858 3.5L13.5182 3.5C13.9233 3.5 14.2518 3.82851 14.2518 4.23377C14.2518 5.9529 16.1129 7.02795 17.602 6.1682C17.9528 5.96567 18.4014 6.08586 18.6039 6.43667L20.1203 9.0631C20.3229 9.41407 20.2027 9.86286 19.8517 10.0655C18.3625 10.9253 18.3625 13.0747 19.8517 13.9345C20.2026 14.1372 20.3229 14.5859 20.1203 14.9369L18.6039 17.5634C18.4013 17.9142 17.9528 18.0344 17.602 17.8318C16.1129 16.9721 14.2518 18.0471 14.2518 19.7663C14.2518 20.1715 13.9233 20.5 13.5182 20.5H10.4858C10.0804 20.5 9.75182 20.1714 9.75182 19.766C9.75182 18.0461 7.88983 16.9717 6.40067 17.8314C6.04945 18.0342 5.60037 17.9139 5.39767 17.5628L3.88167 14.937C3.67903 14.586 3.79928 14.1372 4.15026 13.9346C5.63949 13.0748 5.63946 10.9253 4.15025 10.0655C3.79926 9.86282 3.67901 9.41401 3.88165 9.06303L5.39764 6.43725C5.60034 6.08617 6.04943 5.96581 6.40065 6.16858C7.88982 7.02836 9.75182 5.9539 9.75182 4.23399C9.75182 3.82862 10.0804 3.5 10.4858 3.5ZM9.6659 11.9999C9.6659 10.7103 10.7113 9.66493 12.0009 9.66493C13.2905 9.66493 14.3359 10.7103 14.3359 11.9999C14.3359 13.2895 13.2905 14.3349 12.0009 14.3349C10.7113 14.3349 9.6659 13.2895 9.6659 11.9999Z" fill="currentColor"/></svg>',

            'license' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.75L13.8159 4.05573L16.0451 3.70096L17.0291 5.72567L19.2296 6.35901L19.25 8.66667L21 10L19.25 11.3333L19.2296 13.641L17.0291 14.2743L16.0451 16.299L13.8159 15.9443L12 17.25L10.1841 15.9443L7.95493 16.299L6.97092 14.2743L4.77038 13.641L4.75 11.3333L3 10L4.75 8.66667L4.77038 6.35901L6.97092 5.72567L7.95493 3.70096L10.1841 4.05573L12 2.75ZM12 6.5C9.79086 6.5 8 8.29086 8 10.5C8 12.7091 9.79086 14.5 12 14.5C14.2091 14.5 16 12.7091 16 10.5C16 8.29086 14.2091 6.5 12 6.5ZM9.5 10.5C9.5 9.11929 10.6193 8 12 8C13.3807 8 14.5 9.11929 14.5 10.5C14.5 11.8807 13.3807 13 12 13C10.6193 13 9.5 11.8807 9.5 10.5ZM8.5 17.5C8.5 17.0858 8.16421 16.75 7.75 16.75C7.33579 16.75 7 17.0858 7 17.5V20C7 20.4142 7.33579 20.75 7.75 20.75H16.25C16.6642 20.75 17 20.4142 17 20V17.5C17 17.0858 16.6642 16.75 16.25 16.75C15.8358 16.75 15.5 17.0858 15.5 17.5V19.25H8.5V17.5Z" fill="currentColor"/></svg>',

            'languages' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 17.1086 6.89137 21.25 12 21.25C17.1086 21.25 21.25 17.1086 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM1.25 12C1.25 6.06294 6.06294 1.25 12 1.25C17.9371 1.25 22.75 6.06294 22.75 12C22.75 17.9371 17.9371 22.75 12 22.75C6.06294 22.75 1.25 17.9371 1.25 12ZM12 4.25C12.4142 4.25 12.75 4.58579 12.75 5V7.25H15C15.4142 7.25 15.75 7.58579 15.75 8C15.75 8.41421 15.4142 8.75 15 8.75H12.75V12.75H15C15.4142 12.75 15.75 13.0858 15.75 13.5C15.75 13.9142 15.4142 14.25 15 14.25H12.75V19C12.75 19.4142 12.4142 19.75 12 19.75C11.5858 19.75 11.25 19.4142 11.25 19V14.25H9C8.58579 14.25 8.25 13.9142 8.25 13.5C8.25 13.0858 8.58579 12.75 9 12.75H11.25V8.75H9C8.58579 8.75 8.25 8.41421 8.25 8C8.25 7.58579 8.58579 7.25 9 7.25H11.25V5C11.25 4.58579 11.5858 4.25 12 4.25Z" fill="currentColor"/></svg>',

            'ui' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H18.5C19.7426 20.75 20.75 19.7426 20.75 18.5V5.5C20.75 4.25736 19.7426 3.25 18.5 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H18.5C18.9142 4.75 19.25 5.08579 19.25 5.5V8.25H4.75V5.5ZM4.75 9.75H8.25V19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V9.75ZM9.75 19.25H18.5C18.9142 19.25 19.25 18.9142 19.25 18.5V9.75H9.75V19.25Z" fill="currentColor"/></svg>',

            'updates' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7"/><polyline points="21 4 21 9 16 9"/><line x1="12" y1="8" x2="12" y2="13"/><polyline points="9.5 11 12 13.5 14.5 11"/></svg>',

            'custom-data' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4 5C4 3.89543 4.89543 3 6 3H18C19.1046 3 20 3.89543 20 5V7C20 8.10457 19.1046 9 18 9H6C4.89543 9 4 8.10457 4 7V5ZM6 4.5C5.72386 4.5 5.5 4.72386 5.5 5V7C5.5 7.27614 5.72386 7.5 6 7.5H18C18.2761 7.5 18.5 7.27614 18.5 7V5C18.5 4.72386 18.2761 4.5 18 4.5H6ZM4 11C4 9.89543 4.89543 9 6 9H18C19.1046 9 20 9.89543 20 11V13C20 14.1046 19.1046 15 18 15H6C4.89543 15 4 14.1046 4 13V11ZM6 10.5C5.72386 10.5 5.5 10.7239 5.5 11V13C5.5 13.2761 5.72386 13.5 6 13.5H18C18.2761 13.5 18.5 13.2761 18.5 13V11C18.5 10.7239 18.2761 10.5 18 10.5H6ZM4 17C4 15.8954 4.89543 15 6 15H18C19.1046 15 20 15.8954 20 17V19C20 20.1046 19.1046 21 18 21H6C4.89543 21 4 20.1046 4 19V17ZM6 16.5C5.72386 16.5 5.5 16.7239 5.5 17V19C5.5 19.2761 5.72386 19.5 6 19.5H18C18.2761 19.5 18.5 19.2761 18.5 19V17C18.5 16.7239 18.2761 16.5 18 16.5H6Z" fill="currentColor"/></svg>',

            'pages' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6 2.75C4.75736 2.75 3.75 3.75736 3.75 5V19C3.75 20.2426 4.75736 21.25 6 21.25H18C19.2426 21.25 20.25 20.2426 20.25 19V9C20.25 8.80109 20.171 8.61032 20.0303 8.46967L14.5303 2.96967C14.3897 2.82902 14.1989 2.75 14 2.75H6ZM5.25 5C5.25 4.58579 5.58579 4.25 6 4.25H13.25V9C13.25 9.41421 13.5858 9.75 14 9.75H18.75V19C18.75 19.4142 18.4142 19.75 18 19.75H6C5.58579 19.75 5.25 19.4142 5.25 19V5ZM17.6893 8.25H14.75V5.31066L17.6893 8.25ZM7.75 12C7.75 11.5858 8.08579 11.25 8.5 11.25H15.5C15.9142 11.25 16.25 11.5858 16.25 12C16.25 12.4142 15.9142 12.75 15.5 12.75H8.5C8.08579 12.75 7.75 12.4142 7.75 12ZM8.5 14.75C8.08579 14.75 7.75 15.0858 7.75 15.5C7.75 15.9142 8.08579 16.25 8.5 16.25H13C13.4142 16.25 13.75 15.9142 13.75 15.5C13.75 15.0858 13.4142 14.75 13 14.75H8.5Z" fill="currentColor"/></svg>',
        ];

        return $icons[$iconName] ?? '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" fill="currentColor"/></svg>';
    }
}
