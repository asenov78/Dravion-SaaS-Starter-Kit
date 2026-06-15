@extends('layouts.public')
@section('meta_title', ($galleryPage?->translate(app()->getLocale())?->meta_title ?? __('gallery.title')) . ' — ' . \App\Models\Setting::get('app_name', config('app.name')))
@section('meta_desc', $galleryPage?->translate(app()->getLocale())?->meta_description ?? __('gallery.subtitle'))

@section('content')
@php
    $_g = $galleryPage?->translate(app()->getLocale());
    $galleryBg = $galleryPage?->hero_image ?? 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1920&q=80';
    $galleryTitle = $_g?->hero_title ?? __('gallery.title');
    $gallerySubtitle = $_g?->hero_subtitle ?? __('gallery.subtitle');
@endphp

{{-- Hero --}}
<section class="relative overflow-hidden py-20 sm:py-24 flex items-center"
    style="background-image: url('{{ $galleryBg }}'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-gradient-to-br from-gray-950/85 via-gray-900/75 to-brand-900/60" aria-hidden="true"></div>
    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/10 text-white mb-6 backdrop-blur-sm">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-4 drop-shadow-lg">{{ $galleryTitle }}</h1>
        <p class="text-lg text-white/70 max-w-xl mx-auto">{{ $gallerySubtitle }}</p>
    </div>
</section>

{{-- CMS Content --}}
@if(!empty(trim(strip_tags($_g?->content ?? $galleryPage?->content ?? ''))))
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="cms-content">
        {!! $_g?->content ?? $galleryPage?->content !!}
    </div>
</div>
@else
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <p class="text-center text-gray-400">{{ __('pages.no_content') }}</p>
</div>
@endif
@endsection