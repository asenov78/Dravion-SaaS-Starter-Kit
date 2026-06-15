@extends('layouts.public')
@section('title', config('app.name'))

@section('content')
<div class="max-w-5xl mx-auto px-4 py-20 text-center">
    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-6">{{ config('app.name') }}</h1>
    @if($homePage && $homePage->excerpt)
        <p class="text-xl text-gray-500 dark:text-gray-400 mb-10">{{ $homePage->excerpt }}</p>
    @endif
    @if($homePage && $homePage->content)
        <div class="prose dark:prose-invert mx-auto text-left mt-10">{!! $homePage->content !!}</div>
    @endif
    @guest
    <div class="flex justify-center gap-4 mt-10">
        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-colors">
            {{ __('auth.login') }}
        </a>
        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            {{ __('auth.register') }}
        </a>
    </div>
    @endguest
</div>
@endsection
