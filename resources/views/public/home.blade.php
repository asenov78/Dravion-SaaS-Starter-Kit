@extends('layouts.public')
@section('meta_title', config('app.name') . ' — Complete Laravel SaaS Starter Kit')
@section('meta_desc', 'Production-ready Laravel 13 SaaS with roles, licensing, self-updater, notifications, CMS and more.')

@section('content')

{{-- ══════════════════ HERO ══════════════════ --}}
@php
    $heroBg = $homePage?->hero_image ?? 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1920&q=80';
    $_t = $homePage?->translate(app()->getLocale());
    $heroTitle = $_t?->hero_title ?? null;
    $heroSubtitle = $_t?->hero_subtitle ?? null;
    $heroCtaLabel = $_t?->hero_cta_label ?? null;
    $heroCtaUrl = $homePage?->hero_cta_url ?? null;
@endphp
<section class="relative overflow-hidden py-28 sm:py-36 min-h-[600px] flex items-center"
    style="background-image: url('{{ $heroBg }}'); background-size: cover; background-position: center;">
    {{-- Dark overlay --}}
    <div class="absolute inset-0 bg-gradient-to-br from-gray-950/85 via-gray-900/75 to-brand-900/60" aria-hidden="true"></div>
    {{-- Grid pattern --}}
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <svg class="absolute w-full h-full opacity-[0.04]" xmlns="http://www.w3.org/2000/svg">
            <defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/></pattern></defs>
            <rect width="100%" height="100%" fill="url(#grid)"/>
        </svg>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-white/10 text-white/90 border border-white/20 backdrop-blur-sm mb-8">
            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-brand-400"></span></span>
            Laravel 13 · PHP 8.3 · Tailwind v4 · Alpine.js v3
        </div>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight mb-6 drop-shadow-lg">
            {{ $heroTitle ?? 'Пълноценен SaaS Starter Kit' }}<br>
            <span class="text-brand-400">{{ $heroTitle ? '' : 'готов за продукция' }}</span>
        </h1>
        <p class="text-lg sm:text-xl text-white/75 max-w-2xl mx-auto mb-10 drop-shadow">
            {{ $heroSubtitle ?? 'Спестете месеци разработка. Ролева система, лицензиране, само-обновяване, in-app нотификации, CMS и десетки готови компоненти — всичко в едно.' }}
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
            @guest
            @if($heroCtaUrl && $heroCtaLabel)
            <a href="{{ $heroCtaUrl }}"
               class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-brand-500 text-white font-semibold text-base hover:bg-brand-400 transition-all shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50">
                {{ $heroCtaLabel }}
            </a>
            @else
            <a href="{{ route('register') }}"
               class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-brand-500 text-white font-semibold text-base hover:bg-brand-400 transition-all shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Стартирай безплатно
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl border border-white/30 text-white font-semibold text-base hover:bg-white/10 transition-all backdrop-blur-sm">
                Влез в системата
            </a>
            @endif
            @else
            <a href="{{ auth()->user()->hasAnyRole(['admin','manager','editor']) ? route('admin.dashboard') : route('dashboard') }}"
               class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-brand-500 text-white font-semibold text-base hover:bg-brand-400 transition-all shadow-lg shadow-brand-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Към администрацията
            </a>
            @endguest
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 max-w-3xl mx-auto">
            @foreach([
                ['38+', 'UI Компонента'],
                ['4', 'Роли & права'],
                ['414', 'Теста'],
                ['2', 'Езика'],
            ] as [$num, $label])
            <div class="text-center">
                <div class="text-3xl font-extrabold text-brand-400 mb-1 drop-shadow">{{ $num }}</div>
                <div class="text-sm text-white/60">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════ FEATURES GRID ══════════════════ --}}
<section class="py-20 bg-gray-50 dark:bg-gray-900/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">Всичко, което ви трябва</h2>
            <p class="text-lg text-gray-500 dark:text-gray-400 max-w-xl mx-auto">Не губете време в boilerplate. Фокусирайте се върху бизнес логиката.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @php
            $features = [
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>', 'color' => 'brand', 'title' => 'Управление на потребители', 'desc' => 'Пълен CRUD, bulk actions, suspend/activate, export CSV, role assignment — всичко с activity log.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>', 'color' => 'success', 'title' => 'Ролева система', 'desc' => 'Spatie laravel-permission: admin, manager, editor, user. Fine-grained permission gates на всеки route.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>', 'color' => 'warning', 'title' => 'API Tokens', 'desc' => 'Laravel Sanctum personal access tokens. Създавай, именувай и отменяй токени от потребителския интерфейс.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>', 'color' => 'error', 'title' => 'Activity Log', 'desc' => 'Spatie activitylog: всяко действие е записано. Filters по потребител, събитие и дата. Export CSV.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>', 'color' => 'brand', 'title' => 'In-app Нотификации', 'desc' => 'Bell icon с unread badge. JSON feed, mark as read, mark all. Автоматични нотификации за suspend/activate, нов потребител, обновяване.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>', 'color' => 'success', 'title' => 'Само-обновяване', 'desc' => 'GitHub Releases интеграция. Проверка за нова версия, сваляне, инсталиране — без ръчна намеса. Protected paths.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>', 'color' => 'warning', 'title' => 'Настройки', 'desc' => 'App name, SMTP конфигурация с test, maintenance mode, broadcast banner, default language — всичко от UI.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>', 'color' => 'error', 'title' => 'Многоезичност', 'desc' => 'lang/en + lang/bg. Admin UI за добавяне на локали, inline редакция на всеки ключ, import/export PHP файлове.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>', 'color' => 'brand', 'title' => 'CMS Страници', 'desc' => 'Управлявай публичните страници от администрацията. Title, slug, HTML content, SEO meta, навигационен ред.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>', 'color' => 'success', 'title' => '38+ UI Компонента', 'desc' => 'Button, Card, Badge, Modal, Drawer, Toast, Table, Pagination, Tabs, Accordion, Input, Select и още — всичко shadcn/ui parity.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>', 'color' => 'warning', 'title' => 'Session Management', 'desc' => 'Виж всички активни сесии с IP и browser. Logout other devices с потвърждение на парола. Database sessions.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a2 2 0 10-4 0v5a2 2 0 01-2 2h6m-6-4h4m8 0a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'color' => 'error', 'title' => 'Лицензиране', 'desc' => 'HMAC-based license validation срещу license server. Weekly ping, domain lock, DEV bypass, license warning banner.'],
            ];
            @endphp

            @foreach($features as $f)
            @php $colors = ['brand' => 'bg-brand-50 dark:bg-brand-500/10 text-brand-500', 'success' => 'bg-success-50 dark:bg-success-500/10 text-success-500', 'warning' => 'bg-warning-50 dark:bg-warning-500/10 text-warning-500', 'error' => 'bg-error-50 dark:bg-error-500/10 text-error-500']; @endphp
            <div class="feature-card rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 hover:border-brand-300 dark:hover:border-brand-700 hover:shadow-lg dark:hover:shadow-brand-500/5">
                <div class="w-11 h-11 rounded-xl {{ $colors[$f['color']] }} flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $f['icon'] !!}</svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ $f['title'] }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach

        </div>
    </div>
</section>

{{-- ══════════════════ SECURITY SECTION ══════════════════ --}}
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-error-50 dark:bg-error-500/10 text-error-600 dark:text-error-400 border border-error-200 dark:border-error-500/20 mb-6">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Сигурност
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                    Сигурност на<br>enterprise ниво
                </h2>
                <p class="text-gray-500 dark:text-gray-400 text-lg mb-8">
                    Всеки route е защитен. Rate limiting на всички auth endpoints. Verified email middleware. Double permission check — route + controller.
                </p>
                <ul class="space-y-4">
                    @foreach([
                        'Rate limiting: login 5/мин, register 3/мин, forgot-password 3/мин',
                        'Email верификация с signed URLs (MustVerifyEmail)',
                        'Suspended потребители блокирани преди session creation',
                        'Session regeneration при login и privilege change',
                        'CSRF защита на всички forms и API endpoints',
                        'Avatar upload: MIME validation + GD re-encode (no payload bypass)',
                    ] as $item)
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-success-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $item }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['414', 'Passing Tests', 'success'],
                    ['0', 'Known CVEs', 'success'],
                    ['2FA', 'Coming Soon', 'warning'],
                    ['100%', 'Route Protected', 'brand'],
                ] as [$val, $label, $color])
                @php $bg = ['success' => 'bg-success-50 dark:bg-success-500/10 border-success-200 dark:border-success-500/20', 'warning' => 'bg-warning-50 dark:bg-warning-500/10 border-warning-200 dark:border-warning-500/20', 'brand' => 'bg-brand-50 dark:bg-brand-500/10 border-brand-200 dark:border-brand-500/20'][$color]; @endphp
                @php $tc = ['success' => 'text-success-600 dark:text-success-400', 'warning' => 'text-warning-600 dark:text-warning-400', 'brand' => 'text-brand-600 dark:text-brand-400'][$color]; @endphp
                <div class="rounded-2xl border {{ $bg }} p-6 text-center">
                    <div class="text-3xl font-extrabold {{ $tc }} mb-1">{{ $val }}</div>
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════ STACK SECTION ══════════════════ --}}
<section class="py-20 bg-gray-50 dark:bg-gray-900/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">Модерен технологичен стек</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">Само утвърдени, production-ready технологии. Никакви експериментални зависимости.</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach([
                ['Laravel 13', 'PHP Framework'],
                ['PHP 8.3', 'Backend'],
                ['Tailwind v4', 'CSS'],
                ['Alpine.js v3', 'Reactivity'],
                ['Sanctum', 'API Auth'],
                ['Spatie', 'Roles & Logs'],
            ] as [$tech, $role])
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 text-center hover:border-brand-300 dark:hover:border-brand-700 transition-colors">
                <div class="text-sm font-bold text-gray-900 dark:text-white mb-1">{{ $tech }}</div>
                <div class="text-xs text-gray-400 dark:text-gray-500">{{ $role }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════ ADMIN PREVIEW ══════════════════ --}}
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            {{-- Feature list --}}
            <div class="order-2 lg:order-1">
                <div class="space-y-6">
                    @foreach([
                        ['Global Search', 'Търсене в реално време — потребители, роли, настройки, activity log. 3+ символа → резултати.', 'brand'],
                        ['Broadcast Banner', 'Site-wide съобщения до всички потребители. Dismissable per session. Управлявай от Settings.', 'warning'],
                        ['Dark / Light Mode', 'Запазено в localStorage. Без flash при зареждане. Работи на всеки компонент.', 'success'],
                        ['Installer Wizard', 'Multi-step install: DB, admin акаунт, license, settings. Self-locks след инсталация.', 'error'],
                    ] as [$title, $desc, $color])
                    @php $dot = ['brand' => 'bg-brand-500', 'warning' => 'bg-warning-500', 'success' => 'bg-success-500', 'error' => 'bg-error-500'][$color]; @endphp
                    <div class="flex gap-4">
                        <div class="w-2 h-2 rounded-full {{ $dot }} mt-2 shrink-0"></div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ $title }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $desc }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            {{-- Visual card --}}
            <div class="order-1 lg:order-2">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-hidden shadow-xl">
                    {{-- Fake browser bar --}}
                    <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                        <div class="w-3 h-3 rounded-full bg-error-400"></div>
                        <div class="w-3 h-3 rounded-full bg-warning-400"></div>
                        <div class="w-3 h-3 rounded-full bg-success-400"></div>
                        <div class="flex-1 ml-2 h-5 rounded bg-gray-200 dark:bg-gray-700 text-xs text-gray-400 dark:text-gray-500 flex items-center px-2">
                            localhost/admin/dashboard
                        </div>
                    </div>
                    {{-- Fake dashboard --}}
                    <div class="p-5">
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            @foreach([['12', 'Потребители', 'brand'], ['4', 'Роли', 'success'], ['87', 'Действия', 'warning']] as [$n, $l, $c])
                            @php $tc2 = ['brand' => 'text-brand-500', 'success' => 'text-success-500', 'warning' => 'text-warning-500'][$c]; @endphp
                            <div class="rounded-xl border border-gray-100 dark:border-gray-800 p-3 text-center">
                                <div class="text-xl font-bold {{ $tc2 }}">{{ $n }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $l }}</div>
                            </div>
                            @endforeach
                        </div>
                        <div class="rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
                            <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2">
                                <div class="text-xs font-semibold text-gray-600 dark:text-gray-400">Activity Log</div>
                                <div class="ml-auto w-16 h-5 rounded bg-gray-100 dark:bg-gray-800"></div>
                            </div>
                            @foreach([['Admin logged in', 'преди 2 мин', 'success'], ['User suspended', 'преди 5 мин', 'error'], ['Settings saved', 'преди 12 мин', 'brand']] as [$action, $time, $c])
                            @php $dot3 = ['success' => 'bg-success-400', 'error' => 'bg-error-400', 'brand' => 'bg-brand-400'][$c]; @endphp
                            <div class="flex items-center gap-3 px-4 py-2.5 border-b border-gray-50 dark:border-gray-800/60 last:border-0">
                                <div class="w-2 h-2 rounded-full {{ $dot3 }} shrink-0"></div>
                                <span class="text-xs text-gray-700 dark:text-gray-300 flex-1">{{ $action }}</span>
                                <span class="text-xs text-gray-400">{{ $time }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════ CTA ══════════════════ --}}
<section class="py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="rounded-3xl bg-brand-500 dark:bg-brand-600 p-12 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none opacity-10">
                <svg class="absolute w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs><pattern id="grid2" width="32" height="32" patternUnits="userSpaceOnUse"><path d="M 32 0 L 0 0 0 32" fill="none" stroke="white" stroke-width="1"/></pattern></defs>
                    <rect width="100%" height="100%" fill="url(#grid2)"/>
                </svg>
            </div>
            <div class="relative">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">Готов да стартираш?</h2>
                <p class="text-brand-100 text-lg mb-8 max-w-xl mx-auto">Инсталирай, настрой, изпращай. Без да пишеш boilerplate за месеци.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @guest
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-white text-brand-600 font-semibold text-base hover:bg-brand-50 transition-all shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Стартирай сега
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl border-2 border-white/30 text-white font-semibold text-base hover:bg-white/10 transition-all">
                        Вече имам акаунт
                    </a>
                    @else
                    <a href="{{ auth()->user()->hasAnyRole(['admin','manager','editor']) ? route('admin.dashboard') : route('dashboard') }}"
                       class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-white text-brand-600 font-semibold text-base hover:bg-brand-50 transition-all shadow-lg">
                        Към администрацията →
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>

@endsection