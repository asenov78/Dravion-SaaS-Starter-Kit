<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.register') }} — Dravion</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:#060d1a; font-family:Inter,system-ui,sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; position:relative;">

<x-ui.net-bg />

<div style="width:100%; max-width:380px; padding:0 16px; position:relative; z-index:1;">
    <div style="text-align:center; margin-bottom:32px;">
        <div style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; background:#5e6ad2; border-radius:10px; margin-bottom:16px;">
            <span style="color:#fff; font-weight:700; font-size:18px;">D</span>
        </div>
        <h1 style="color:#e2e2e9; font-size:18px; font-weight:600; margin:0 0 4px;">{{ __('auth.title_register') }}</h1>
        <p style="color:#6b6b7b; font-size:13px; margin:0;">{{ __('auth.subtitle_register') }}</p>
    </div>

    <x-ui.card>
        @if($errors->any())
        <x-ui.alert variant="error" class="mb-4">{{ $errors->first() }}</x-ui.alert>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div style="display:flex; flex-direction:column; gap:14px;">
                <x-ui.input name="name" :label="__('auth.full_name')" :value="old('name')" :error="$errors->first('name')" required autofocus />
                <x-ui.input name="email" type="email" :label="__('auth.email')" :value="old('email')" :error="$errors->first('email')" required />
                <x-ui.input name="password" type="password" :label="__('auth.password_label')" :error="$errors->first('password')" required />
                <x-ui.input name="password_confirmation" type="password" :label="__('auth.confirm_password')" required />
                <x-ui.button type="submit" style="width:100%;">{{ __('auth.create') }}</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <p style="text-align:center; margin-top:16px; color:#6b6b7b; font-size:12px;">
        {{ __('auth.have_account') }}
        <a href="{{ route('login') }}" style="color:#5e6ad2; text-decoration:none;"
            onmouseover="this.style.color='#7b84e0'" onmouseout="this.style.color='#5e6ad2'">{{ __('auth.login') }}</a>
    </p>
</div>

</body>
</html>
