@extends('layouts.public')
@php $_p = $page->translate(app()->getLocale()); @endphp
@section('meta_title', ($_p->meta_title ?? $page->title) . ' — ' . \App\Models\Setting::get('app_name', config('app.name')))
@section('meta_desc', $_p->meta_description ?? $page->excerpt ?? '')

@section('content')
@if($page->hero_image)
<section class="relative overflow-hidden py-16 sm:py-20 flex items-center"
    style="background-image: url('{{ $page->hero_image }}'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-gradient-to-br from-gray-950/85 via-gray-900/75 to-brand-900/60" aria-hidden="true"></div>
    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-6">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-3 drop-shadow-lg">{{ $_p->hero_title ?? $_p->title }}</h1>
        @if($_p->hero_subtitle)
        <p class="text-lg text-white/70 max-w-xl mx-auto">{{ $_p->hero_subtitle }}</p>
        @endif
    </div>
</section>
@endif

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    @if(!$page->hero_image)
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $_p->title }}</h1>
    @endif
    @if($_p->excerpt)
    <p class="text-lg text-gray-500 dark:text-gray-400 mb-8 border-l-4 border-brand-400 pl-4">{{ $_p->excerpt }}</p>
    @endif
    @if(!empty(trim(strip_tags($_p->content ?? ''))))
    <div class="cms-content">{!! $_p->content !!}</div>
    @endif
</div>
@endsection