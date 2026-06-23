@php
    $isAdmin = auth()->user()?->hasAnyRole(['admin','manager','editor']);
    $layout = $isAdmin ? 'layouts.admin' : 'layouts.portal';
    $backRoute = $isAdmin ? route('admin.dashboard') : route('dashboard');
@endphp

<x-dynamic-component :component="$layout" :title="__('sessions.title')">

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('sessions.title') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('sessions.subtitle') }}</p>
    </div>
    <a href="{{ $backRoute }}"
        class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        {{ __('app.back') }}
    </a>
</div>

@if(session('success'))
    <x-ui.alert variant="success" class="mb-5">{{ session('success') }}</x-ui.alert>
@endif

{{-- Active sessions list --}}
@if($sessions->isNotEmpty())
<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 mb-5">
    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-4">{{ __('sessions.active') }}</h3>
    <div class="divide-y divide-gray-100 dark:divide-gray-800">
        @foreach($sessions as $session)
        <div class="flex items-start gap-4 py-4">
            <div class="mt-0.5 text-{{ $session->is_current ? 'success-500' : 'gray-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">
                        {{ $session->ip_address ?? __('sessions.unknown_ip') }}
                    </span>
                    @if($session->is_current)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                            {{ __('sessions.this_device') }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">
                    {{ Str::limit($session->user_agent ?? __('sessions.unknown_browser'), 80) }}
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    {{ __('sessions.last_active') }}: {{ $session->last_activity->diffForHumans() }}
                </p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Logout other devices --}}
<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900"
    x-data="{open: false}">
    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-1">{{ __('sessions.logout_others_title') }}</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('sessions.logout_others_desc') }}</p>

    <button @click="open = !open"
        class="inline-flex items-center gap-2 rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-error-600 transition-colors">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        {{ __('sessions.logout_others_btn') }}
    </button>

    <div x-show="open" x-cloak class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <form method="POST" action="{{ route('sessions.logout-others') }}">
            @csrf
            <div class="flex gap-3 items-start">
                <div class="flex-1">
                    <x-ui.input name="password" type="password" :placeholder="__('auth.password_label')" />
                    @error('password')
                        <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                    @enderror
                </div>
                <x-ui.button type="submit" variant="danger">{{ __('app.confirm') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>

</x-dynamic-component>
