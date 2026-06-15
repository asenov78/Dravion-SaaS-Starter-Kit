<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.title_reset') }} — Dravion</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:#060d1a; font-family:Inter,system-ui,sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; position:relative;">

<x-ui.net-bg />

<div style="width:100%; max-width:360px; padding:0 16px; position:relative; z-index:1;">
    <div style="text-align:center; margin-bottom:32px;">
        <div style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; background:#5e6ad2; border-radius:10px; margin-bottom:16px;">
            <span style="color:#fff; font-weight:700; font-size:18px;">D</span>
        </div>
        <h1 style="color:#e2e2e9; font-size:18px; font-weight:600; margin:0 0 4px;">{{ __('auth.title_reset') }}</h1>
        <p style="color:#6b6b7b; font-size:13px; margin:0;">{{ __('auth.subtitle_reset') }}</p>
    </div>

    <x-ui.card>
        @if($errors->any())
        <x-ui.alert variant="error" class="mb-4">{{ $errors->first() }}</x-ui.alert>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div style="display:flex; flex-direction:column; gap:14px;">
                <x-ui.input name="email" type="email" :label="__('auth.email')" :value="old('email')" required autofocus />
                <x-ui.input name="password" type="password" :label="__('auth.new_password')" required />
                <x-ui.input name="password_confirmation" type="password" :label="__('auth.confirm_password')" required />
                <x-ui.button type="submit" style="width:100%;">{{ __('auth.reset') }}</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>

</body>
</html>
