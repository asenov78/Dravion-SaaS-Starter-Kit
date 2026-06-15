@extends('layouts.public')
@section('meta_title', __('contact.title') . ' — ' . config('app.name'))

@section('content')
@php
    $contactBg = $contactPage?->hero_image ?? 'https://images.unsplash.com/photo-1516387938699-a93567ec168e?auto=format&fit=crop&w=1920&q=80';
    $_ct = $contactPage?->translate(app()->getLocale());
    $contactTitle = $_ct?->hero_title ?? __('contact.title');
    $contactSubtitle = $_ct?->hero_subtitle ?? __('contact.subtitle');
@endphp
{{-- Hero --}}
<section class="relative overflow-hidden py-20 sm:py-24 flex items-center"
    style="background-image: url('{{ $contactBg }}'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-gradient-to-br from-gray-950/85 via-gray-900/75 to-brand-900/60" aria-hidden="true"></div>
    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/10 text-white mb-6 backdrop-blur-sm">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-4 drop-shadow-lg">{{ $contactTitle }}</h1>
        <p class="text-lg text-white/70 max-w-xl mx-auto">{{ $contactSubtitle }}</p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">

    @if(!empty(trim(strip_tags($_ct?->content ?? $contactPage?->content ?? ''))))
    <div class="max-w-3xl mx-auto mb-12 cms-content">
        {!! $_ct?->content ?? $contactPage?->content !!}
    </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-10">

        {{-- Info cards --}}
        <div class="space-y-5">
            @foreach([
                ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => __('contact.email_label'), 'value' => \App\Models\Setting::get('admin_email', config('mail.from.address', 'hello@example.com')), 'color' => 'brand'],
                ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => __('contact.response_label'), 'value' => __('contact.response_time'), 'color' => 'success'],
                ['icon' => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z', 'label' => __('contact.support_label'), 'value' => __('contact.support_value'), 'color' => 'warning'],
            ] as $info)
            @php $clr = ['brand' => 'bg-brand-50 dark:bg-brand-500/10 text-brand-500', 'success' => 'bg-success-50 dark:bg-success-500/10 text-success-500', 'warning' => 'bg-warning-50 dark:bg-warning-500/10 text-warning-500'][$info['color']]; @endphp
            <div class="flex items-start gap-4 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $clr }} flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-0.5">{{ $info['label'] }}</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $info['value'] }}</p>
                </div>
            </div>
            @endforeach

            {{-- Decorative blob --}}
            <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-brand-500 to-purple-600 p-6 text-white">
                <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-white/10"></div>
                <div class="absolute -bottom-6 -left-6 w-24 h-24 rounded-full bg-white/10"></div>
                <div class="relative">
                    <p class="font-semibold mb-2">{{ __('contact.cta_title') }}</p>
                    <p class="text-sm text-white/80">{{ __('contact.cta_body') }}</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-8">

                @if(session('contact_success'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-success-200 dark:border-success-500/20 bg-success-50 dark:bg-success-500/10 px-4 py-4">
                    <svg class="w-5 h-5 text-success-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-medium text-success-700 dark:text-success-400">{{ __('contact.success') }}</p>
                </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('contact.name') }} <span class="text-error-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-brand-300 dark:focus:border-brand-600 focus:ring-2 focus:ring-brand-500/20 focus:outline-none transition-colors"
                                placeholder="Иван Иванов">
                            @error('name')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('contact.email') }} <span class="text-error-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-brand-300 dark:focus:border-brand-600 focus:ring-2 focus:ring-brand-500/20 focus:outline-none transition-colors"
                                placeholder="ivan@example.com">
                            @error('email')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('contact.subject') }}</label>
                        <input type="text" name="subject" value="{{ old('subject') }}"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-brand-300 dark:focus:border-brand-600 focus:ring-2 focus:ring-brand-500/20 focus:outline-none transition-colors"
                            placeholder="{{ __('contact.subject_placeholder') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('contact.message') }} <span class="text-error-500">*</span></label>
                        <textarea name="message" rows="6" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-brand-300 dark:focus:border-brand-600 focus:ring-2 focus:ring-brand-500/20 focus:outline-none transition-colors resize-none"
                            placeholder="{{ __('contact.message_placeholder') }}">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-6 py-3 text-sm font-semibold text-white hover:bg-brand-600 transition-colors shadow-sm shadow-brand-500/25 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        {{ __('contact.send') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection