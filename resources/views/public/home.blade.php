@extends('layouts.public')
@section('meta_title', $_t?->meta_title ?? config('app.name') . ' — Complete Laravel SaaS Starter Kit')
@section('meta_desc', $_t?->meta_description ?? 'Production-ready Laravel 13 SaaS with roles, licensing, self-updater, notifications, CMS and more.')

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

{{-- ══════════════════ CMS CONTENT ══════════════════ --}}
<section class="py-16 bg-white dark:bg-gray-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="cms-content">
            {!! $_t?->content ?? $homePage?->content !!}
        </div>
    </div>
</section>

@endsection
