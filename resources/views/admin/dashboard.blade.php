<x-layouts.admin :title="__('nav.dashboard')">

@php
$totalUsers     = \App\Models\User::count();
$activeUsers    = \App\Models\User::where('status','active')->count();
$suspendedUsers = \App\Models\User::where('status','suspended')->count();
$recentUsers    = \App\Models\User::with('roles')->latest()->take(8)->get();
$adminCount     = \App\Models\User::role('admin')->count();
$managerCount   = \App\Models\User::role('manager')->count();
$regularUsers   = $totalUsers - $adminCount - $managerCount;
$newThisMonth   = $newThisMonth ?? 0;
$recentActivity = $recentActivity ?? collect();
@endphp

{{-- Page title --}}
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('nav.dashboard') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('dashboard.subtitle') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.users.create') }}"
            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            {{ __('users.add') }}
        </a>
    </div>
</div>

{{-- Stats grid --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-6">
    {{-- Total Users --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between mb-4">
            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" class="text-brand-500"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.5C10.0617 3.5 8.5 5.06168 8.5 6.99998C8.5 8.93829 10.0617 10.5 12 10.5C13.9383 10.5 15.5 8.93829 15.5 6.99998C15.5 5.06168 13.9383 3.5 12 3.5ZM7 6.99998C7 4.23327 9.23328 2 12 2C14.7667 2 17 4.23327 17 6.99998C17 9.7667 14.7667 12 12 12C9.23328 12 7 9.7667 7 6.99998ZM5.5 20.5H18.5C18.2239 18.1333 16.3267 16.5 12 16.5C7.67328 16.5 5.77606 18.1333 5.5 20.5ZM4 21C4 17.134 7.13401 15 12 15C16.866 15 20 17.134 20 21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21Z" fill="currentColor"/></svg>
            </span>
        </div>
        <h4 class="text-3xl font-bold text-gray-800 dark:text-white/90 mb-1">{{ $totalUsers }}</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('users.total') }}</p>
    </div>

    {{-- Active Users --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between mb-4">
            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-success-50 dark:bg-success-500/10">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" class="text-success-500"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 17.1086 6.89137 21.25 12 21.25C17.1086 21.25 21.25 17.1086 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM1.25 12C1.25 6.06294 6.06294 1.25 12 1.25C17.9371 1.25 22.75 6.06294 22.75 12C22.75 17.9371 17.9371 22.75 12 22.75C6.06294 22.75 1.25 17.9371 1.25 12ZM16.0303 9.46967C16.3232 9.76256 16.3232 10.2374 16.0303 10.5303L11.5303 15.0303C11.2374 15.3232 10.7626 15.3232 10.4697 15.0303L7.96967 12.5303C7.67678 12.2374 7.67678 11.7626 7.96967 11.4697C8.26256 11.1768 8.73744 11.1768 9.03033 11.4697L11 13.4393L14.9697 9.46967C15.2626 9.17678 15.7374 9.17678 16.0303 9.46967Z" fill="currentColor"/></svg>
            </span>
        </div>
        <h4 class="text-3xl font-bold text-gray-800 dark:text-white/90 mb-1">{{ $activeUsers }}</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('users.active_count') }}</p>
    </div>

    {{-- Suspended --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between mb-4">
            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-error-50 dark:bg-error-500/10">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" class="text-error-500"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 17.1086 6.89137 21.25 12 21.25C17.1086 21.25 21.25 17.1086 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM9.53033 8.46967C9.23744 8.17678 8.76256 8.17678 8.46967 8.46967C8.17678 8.76256 8.17678 9.23744 8.46967 9.53033L10.9393 12L8.46967 14.4697C8.17678 14.7626 8.17678 15.2374 8.46967 15.5303C8.76256 15.8232 9.23744 15.8232 9.53033 15.5303L12 13.0607L14.4697 15.5303C14.7626 15.8232 15.2374 15.8232 15.5303 15.5303C15.8232 15.2374 15.8232 14.7626 15.5303 14.4697L13.0607 12L15.5303 9.53033C15.8232 9.23744 15.8232 8.76256 15.5303 8.46967C15.2374 8.17678 14.7626 8.17678 14.4697 8.46967L12 10.9393L9.53033 8.46967Z" fill="currentColor"/></svg>
            </span>
        </div>
        <h4 class="text-3xl font-bold text-gray-800 dark:text-white/90 mb-1">{{ $suspendedUsers }}</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.suspended_count') }}</p>
    </div>

    {{-- Admins --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between mb-4">
            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-warning-50 dark:bg-warning-500/10">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" class="text-warning-500"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.75L13.8159 4.05573L16.0451 3.70096L17.0291 5.72567L19.2296 6.35901L19.25 8.66667L21 10L19.25 11.3333L19.2296 13.641L17.0291 14.2743L16.0451 16.299L13.8159 15.9443L12 17.25L10.1841 15.9443L7.95493 16.299L6.97092 14.2743L4.77038 13.641L4.75 11.3333L3 10L4.75 8.66667L4.77038 6.35901L6.97092 5.72567L7.95493 3.70096L10.1841 4.05573L12 2.75Z" fill="currentColor"/></svg>
            </span>
        </div>
        <h4 class="text-3xl font-bold text-gray-800 dark:text-white/90 mb-1">{{ $adminCount }}</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.admins') }}</p>
    </div>
</div>

{{-- New This Month --}}
<div class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('users.new_month') }}</p>
        <h4 class="text-3xl font-bold text-gray-800 dark:text-white/90">{{ $newThisMonth }}</h4>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('users.total') }}</p>
        <h4 class="text-3xl font-bold text-gray-800 dark:text-white/90">{{ $totalUsers }}</h4>
    </div>
</div>

{{-- Recent Users --}}
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('users.recent') }}</h3>
        <a href="{{ route('admin.users.index') }}"
            class="text-sm text-brand-500 hover:text-brand-600 font-medium">{{ __('dashboard.view_all') }} →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('app.name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('app.role') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('app.status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('app.created_at') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($recentUsers as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-9 h-9 rounded-full bg-brand-500 text-white text-sm font-semibold flex-shrink-0 overflow-hidden">
                                @if($user->avatar)
                                    <img src="{{ url('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3">
                        <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                            {{ $user->getRoleNames()->first() ?? 'user' }}
                        </span>
                    </td>
                    <td class="px-6 py-3">
                        @if($user->status === 'active')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>{{ __('app.active') }}
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-error-500"></span>{{ __('app.suspended') }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ $user->created_at->locale(app()->getLocale())->isoFormat('D MMM YYYY') }}
                    </td>
                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('admin.users.edit', $user) }}"
                            class="text-sm text-brand-500 hover:text-brand-600 font-medium">{{ __('app.edit') }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                        {{ __('dashboard.no_users') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Recent Activity --}}
<div class="mt-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('dashboard.recent_activity') }}</h3>
        <a href="{{ route('admin.activity') }}"
            class="text-sm text-brand-500 hover:text-brand-600 font-medium">{{ __('dashboard.view_all') }} →</a>
    </div>
    <div class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse($recentActivity as $log)
        <div class="flex items-start gap-4 px-6 py-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-brand-50 dark:bg-brand-500/10 flex-shrink-0 mt-0.5">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="text-brand-500"><path fill-rule="evenodd" clip-rule="evenodd" d="M2.75 12C2.75 6.89137 6.89137 2.75 12 2.75C17.1086 2.75 21.25 6.89137 21.25 12C21.25 17.1086 17.1086 21.25 12 21.25C6.89137 21.25 2.75 17.1086 2.75 12ZM12 1.25C6.06294 1.25 1.25 6.06294 1.25 12C1.25 17.9371 6.06294 22.75 12 22.75C17.9371 22.75 22.75 17.9371 22.75 12C22.75 6.06294 17.9371 1.25 12 1.25ZM12.75 7C12.75 6.58579 12.4142 6.25 12 6.25C11.5858 6.25 11.25 6.58579 11.25 7V12C11.25 12.2652 11.3817 12.5136 11.6 12.66L15.1 15.16C15.4314 15.3948 15.8943 15.3157 16.129 14.9843C16.3638 14.6528 16.2847 14.1899 15.9533 13.9552L12.75 11.7V7Z" fill="currentColor"/></svg>
            </span>
            <div class="flex-1 min-w-0">
                @php
                    $logKey    = $log->getExtraProperty('desc_key');
                    $logParams = $log->getExtraProperty('desc_params', []);
                    $logDesc   = $logKey ? __($logKey, $logParams) : $log->description;
                @endphp
                <p class="text-sm text-gray-800 dark:text-white/80 truncate">{{ $logDesc }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ optional($log->causer)->name ?? __('activity.system') }} · {{ $log->created_at->diffForHumans() }}
                </p>
            </div>
            <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 flex-shrink-0">
                {{ $log->log_name }}
            </span>
        </div>
        @empty
        <div class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
            {{ __('dashboard.no_activity') }}
        </div>
        @endforelse
    </div>
</div>

{{-- System Health --}}
<div class="mt-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('settings.system_health') }}</h3>
        @can('edit settings')
        <form method="POST" action="{{ route('admin.cache.clear') }}">
            @csrf
            <button type="submit" class="text-sm text-brand-500 hover:text-brand-600 font-medium">{{ __('dashboard.clear_cache') }}</button>
        </form>
        @endcan
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 divide-x divide-y divide-gray-100 dark:divide-gray-800">
        @php
        $healthItems = [
            ['label' => 'PHP',          'value' => 'PHP ' . $health['php_version']],
            ['label' => 'Laravel',      'value' => 'v' . $health['laravel_version']],
            ['label' => 'Memory Limit', 'value' => $health['memory_limit']],
            ['label' => 'Max Upload',   'value' => $health['max_upload']],
            ['label' => 'Disk Used',    'value' => $health['disk_used_pct'] . '%',
             'warn' => $health['disk_used_pct'] > 85],
            ['label' => 'Disk Free',    'value' => $health['disk_free_gb'] . ' GB'],
            ['label' => 'DB Size',      'value' => $health['db_size_kb'] . ' KB'],
            ['label' => 'Cache Driver', 'value' => $health['cache_driver']],
        ];
        @endphp
        @foreach($healthItems as $item)
        <div class="px-5 py-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $item['label'] }}</p>
            <p class="text-sm font-semibold {{ ($item['warn'] ?? false) ? 'text-error-600 dark:text-error-400' : 'text-gray-800 dark:text-white/90' }}">
                {{ $item['value'] }}
            </p>
        </div>
        @endforeach
    </div>
</div>

</x-layouts.admin>
