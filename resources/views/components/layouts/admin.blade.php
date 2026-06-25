@props(['title' => 'Admin'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ \App\Models\Setting::get('app_name', config('app.name')) }}</title>
    @php
        $fpLocale   = session('locale') ?? \App\Models\Setting::get('default_language', 'en');
        $fpFirstDay = (int) \App\Models\Setting::get('week_start', '1');
    @endphp
    <script>
    window.appLocale = '{{ $fpLocale }}';
    window.appFirstDayOfWeek = {{ $fpFirstDay }};
    @if($fpLocale === 'bg')
    window.fpConfig={weekdays:{shorthand:['Нед','Пон','Вт','Ср','Чет','Пет','Съб'],longhand:['Неделя','Понеделник','Вторник','Сряда','Четвъртък','Петък','Събота']},months:{shorthand:['Яну','Фев','Мар','Апр','Май','Юни','Юли','Авг','Сеп','Окт','Ное','Дек'],longhand:['Януари','Февруари','Март','Април','Май','Юни','Юли','Август','Септември','Октомври','Ноември','Декември']},firstDayOfWeek:{{ $fpFirstDay }},ordinal:function(){return'.';}};
    @else
    window.fpConfig={firstDayOfWeek:{{ $fpFirstDay }}};
    @endif
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine stores --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const saved = localStorage.getItem('theme');
                    const sys = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    this.theme = saved || sys;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    if (this.theme === 'dark') {
                        document.documentElement.classList.add('dark');
                        document.body.classList.add('dark', 'bg-gray-900');
                    } else {
                        document.documentElement.classList.remove('dark');
                        document.body.classList.remove('dark', 'bg-gray-900');
                    }
                }
            });

            Alpine.store('flash', {
                show: false,
                message: '',
                variant: 'success',
                _timer: null,
                fire(message, variant = 'success', duration = 4000) {
                    clearTimeout(this._timer);
                    this.message = message;
                    this.variant = variant;
                    this.show = true;
                    this._timer = setTimeout(() => { this.show = false; }, duration);
                }
            });

            Alpine.store('broadcast', {
                content: @js(\App\Models\Setting::getLocalized('broadcast_banner', '')),
                dismissed: false,
                init() {
                    this.dismissed = sessionStorage.getItem('banner_dismissed') === this.content;
                },
                dismiss() {
                    sessionStorage.setItem('banner_dismissed', this.content);
                    this.dismissed = true;
                }
            });

            Alpine.store('sidebar', {
                isExpanded: window.innerWidth >= 1280
                    ? (localStorage.getItem('sidebarExpanded') !== 'false')
                    : false,
                isMobileOpen: false,
                isHovered: false,
                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    this.isMobileOpen = false;
                    localStorage.setItem('sidebarExpanded', this.isExpanded);
                },
                toggleMobileOpen() { this.isMobileOpen = !this.isMobileOpen; },
                setMobileOpen(val) { this.isMobileOpen = val; },
                setHovered(val) {
                    if (window.innerWidth >= 1280 && !this.isExpanded) this.isHovered = val;
                }
            });
        });
    </script>

    {{-- Session flash → Alpine store bridge --}}
    @if(session('success') || session('error') || session('warning'))
    <script>
        document.addEventListener('alpine:init', () => {
            @if(session('success'))
            Alpine.store('flash').fire(@json(session('success')), 'success');
            @endif
            @if(session('warning'))
            Alpine.store('flash').fire(@json(session('warning')), 'warning');
            @endif
            @if(session('error'))
            Alpine.store('flash').fire(@json(session('error')), 'error');
            @endif
        });
    </script>
    @endif

    {{-- Prevent dark mode flash --}}
    <script>
        (function() {
            const t = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (t === 'dark') {
                document.documentElement.classList.add('dark');
                document.body && document.body.classList.add('dark', 'bg-gray-900');
            }
        })();
    </script>
</head>

<body
    x-data="{ loaded: true }"
    x-init="
        window.addEventListener('resize', () => {
            if (window.innerWidth < 1280) {
                $store.sidebar.setMobileOpen(false);
                $store.sidebar.isExpanded = false;
            } else {
                $store.sidebar.isMobileOpen = false;
                $store.sidebar.isExpanded = localStorage.getItem('sidebarExpanded') !== 'false';
            }
        });
    ">

    <x-common.preloader />

    <div class="min-h-screen xl:flex">
        @include('layouts.backdrop')
        @include('layouts.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered
            }">
            @include('layouts.app-header')

            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                {{-- Inline flash alert --}}
                <div x-data
                     x-show="$store.flash.show"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     :class="{
                         'border-success-200 bg-success-50 text-success-700 dark:border-success-800 dark:bg-success-500/10 dark:text-success-400': $store.flash.variant === 'success',
                         'border-warning-200 bg-warning-50 text-warning-700 dark:border-warning-800 dark:bg-warning-500/10 dark:text-warning-400': $store.flash.variant === 'warning',
                         'border-error-200 bg-error-50 text-error-700 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400': $store.flash.variant === 'error'
                     }"
                     class="mb-5 flex items-center gap-3 rounded-lg border px-4 py-3 text-sm"
                     x-cloak>
                    <svg x-show="$store.flash.variant === 'success'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                    <svg x-show="$store.flash.variant === 'warning'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <svg x-show="$store.flash.variant === 'error'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span class="flex-1" x-text="$store.flash.message"></span>
                    <button type="button" @click="$store.flash.show = false" class="ml-auto opacity-50 hover:opacity-80 transition-opacity">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- License Warning Banner --}}
                @php $noLicense = empty(config('dravion.license_key')); @endphp
                @if($noLicense)
                <div class="mb-5 flex items-center gap-3 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span class="flex-1">{{ session('license_warning') ?: __('license.no_key') }}</span>
                    <a href="{{ route('admin.updates') }}" class="shrink-0 font-medium underline hover:no-underline">{{ __('updates.go_to_license') }}</a>
                </div>
                @endif

                {{-- Broadcast Banner --}}
                @if(\App\Models\Setting::getLocalized('broadcast_banner', ''))
                <div id="broadcast-banner"
                     x-data
                     x-show="$store.broadcast.content && !$store.broadcast.dismissed"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="mb-5 flex items-center gap-3 rounded-lg border border-warning-200 bg-warning-50 px-4 py-3 text-sm text-warning-700 dark:border-warning-800 dark:bg-warning-500/10 dark:text-warning-400"
                     x-cloak>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span class="flex-1" x-text="$store.broadcast.content">{{ \App\Models\Setting::get('broadcast_banner') }}</span>
                    <button type="button" @click="$store.broadcast.dismiss()" class="ml-auto opacity-50 hover:opacity-80 transition-opacity" aria-label="{{ __('app.cancel') }}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                @endif

                @php
                    $licenseBlur = empty(config('dravion.license_key'))
                        && ! request()->routeIs('admin.updates')
                        && ! request()->routeIs('admin.license');
                @endphp
                <div @if($licenseBlur) style="filter:blur(2px);pointer-events:none;user-select:none;" @endif>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Global Confirm Modal --}}
<div x-data="{
    open: false,
    loading: false,
    title: '',
    message: '',
    btnLabel: '',
    btnColor: '',
    url: '',
    method: 'POST',
    successAction: '',
    targetId: '',
    userId: null,
    toastMessage: '',
    toastVariant: 'success',
    show(d) {
        this.title = d.title;
        this.message = d.message;
        this.btnLabel = d.btnLabel;
        this.btnColor = d.btnColor;
        this.url = d.url;
        this.method = d.method || 'POST';
        this.successAction = d.successAction || '';
        this.targetId = d.targetId || '';
        this.userId = d.userId || null;
        this.toastMessage = d.toastMessage || '';
        this.toastVariant = d.toastVariant || 'success';
        this.open = true;
    },
    flashRow(rowId, cls) {
        const row = document.getElementById(rowId);
        if (!row) return;
        row.classList.add(cls);
        row.addEventListener('animationend', () => row.classList.remove(cls), { once: true });
    },
    async confirm() {
        this.loading = true;
        const csrf = document.querySelector('meta[name=csrf-token]').content;
        const body = new URLSearchParams({ _token: csrf, _method: this.method });
        try {
            const res = await fetch(this.url, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body
            });
            if (res.ok) {
                if (this.toastMessage) {
                    Alpine.store('flash').fire(this.toastMessage, this.toastVariant);
                }
                if (this.successAction === 'remove') {
                    const row = document.getElementById(this.targetId);
                    if (row) {
                        row.classList.add('row-flash-error');
                        setTimeout(() => row.remove(), 550);
                    }
                } else if (this.userId) {
                    const flashCls = this.successAction === 'active' ? 'row-flash-success' : 'row-flash-warning';
                    this.flashRow('row-' + this.userId, flashCls);
                    window.dispatchEvent(new CustomEvent('user-status-updated', { detail: { id: this.userId, newStatus: this.successAction } }));
                } else if (this.successAction === 'redirect') {
                    window.location = this.targetId;
                }
            }
        } catch (e) { console.error(e); }
        this.loading = false;
        this.open = false;
    }
}" @confirm-action.window="show($event.detail)"
   x-show="open" x-cloak @keydown.escape.window="open = false"
   class="fixed inset-0 flex items-center justify-center p-5" style="z-index:99999">

    <div @click="open = false"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/60"></div>

    <div @click.stop
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl dark:border-gray-800 dark:bg-gray-900">

        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-full bg-error-500/10 border border-error-500/20">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="text-error-500 dark:text-error-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-base font-semibold text-gray-800 mb-1 dark:text-white/90" x-text="title"></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="message"></p>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button @click="open = false"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800">
                {{ __('app.cancel') }}
            </button>
            <button @click="confirm()" :disabled="loading"
                :style="'background:' + btnColor"
                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors disabled:opacity-70">
                <svg x-show="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                <span x-text="btnLabel"></span>
            </button>
        </div>
    </div>
</div>

</body>
</html>
