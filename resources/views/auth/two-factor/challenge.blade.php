<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.2fa_title') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:#060d1a; font-family:Inter,system-ui,sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; position:relative;">

<x-ui.net-bg />

<div style="width:100%; max-width:400px; padding:0 16px; position:relative; z-index:1;">
    <div style="text-align:center; margin-bottom:32px;">
        <div style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; background:#5e6ad2; border-radius:10px; margin-bottom:16px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <h1 style="color:#e2e2e9; font-size:18px; font-weight:600; margin:0 0 4px;">{{ __('auth.2fa_title') }}</h1>
        <p style="color:#6b6b7b; font-size:13px; margin:0;">{{ __('auth.2fa_subtitle') }}</p>
    </div>

    <x-ui.card>
        @if ($errors->any())
            <x-ui.alert variant="error" class="mb-4">{{ $errors->first() }}</x-ui.alert>
        @endif

        <form method="POST" action="{{ route('two-factor.verify') }}">
            @csrf
            <div style="margin-bottom:16px;">
                <x-ui.label for="code">{{ __('auth.2fa_code') }}</x-ui.label>
                <x-ui.input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]{6}"
                    maxlength="6" autocomplete="one-time-code" autofocus
                    placeholder="000000" style="text-align:center; letter-spacing:0.3em; font-size:18px;" />
            </div>
            <x-ui.button type="submit" style="width:100%;">{{ __('auth.2fa_verify') }}</x-ui.button>
        </form>
    </x-ui.card>

    <p style="text-align:center; margin-top:16px; color:#6b6b7b; font-size:12px;">
        <a href="{{ route('login') }}" style="color:#5e6ad2; text-decoration:none;">{{ __('auth.back_to_login') }}</a>
    </p>
</div>

</body>
</html>
