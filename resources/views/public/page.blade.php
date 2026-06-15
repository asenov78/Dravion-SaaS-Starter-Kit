@extends('layouts.public')
@section('title', $page->meta_title ?? $page->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ $page->title }}</h1>
    @if($page->excerpt)
        <p class="text-lg text-gray-500 dark:text-gray-400 mb-8">{{ $page->excerpt }}</p>
    @endif
    <div class="prose dark:prose-invert max-w-none">{!! $page->content !!}</div>
</div>
@endsection
