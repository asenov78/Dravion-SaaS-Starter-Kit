<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __("auth.verify_title") }} — Dravion</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(["resources/css/app.css", "resources/js/app.js"])
</head>
<body style="background:#060d1a; font-family:Inter,system-ui,sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; position:relative;">

<x-ui.net-bg />

<div style="width:100%; max-width:400px; padding:0 16px; position:relative; z-index:1;">
    <div style="text-align:center; margin-bottom:32px;">
        <div style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; background:#5e6ad2; border-radius:10px; margin-bottom:16px;">
            <span style="color:#fff; font-weight:700; font-size:18px;">D</span>
        </div>
        <h1 style="color:#e2e2e9; font-size:18px; font-weight:600; margin:0 0 4px;">{{ __("auth.verify_title") }}</h1>
        <p style="color:#6b6b7b; font-size:13px; margin:0;">{{ __("auth.verify_subtitle") }}</p>
    </div>

    <x-ui.card>
        @if(session("resent"))
        <x-ui.alert variant="success" class="mb-4">{{ __("auth.verify_resent") }}</x-ui.alert>
        @endif

        <p style="color:#9e9eae; font-size:13px; line-height:1.6; margin:0 0 20px;">
            {{ __("auth.verify_instructions") }}
        </p>

        <form method="POST" action="{{ route("verification.send") }}">
            @csrf
            <x-ui.button type="submit" style="width:100%;">{{ __("auth.verify_resend") }}</x-ui.button>
        </form>
    </x-ui.card>

    <p style="text-align:center; margin-top:16px; color:#6b6b7b; font-size:12px;">
        <form method="POST" action="{{ route("logout") }}" style="display:inline;">
            @csrf
            <button type="submit" style="background:none; border:none; color:#5e6ad2; font-size:12px; cursor:pointer; text-decoration:none;"
                onmouseover="this.style.color='#7b84e0'" onmouseout="this.style.color='#5e6ad2'">{{ __("auth.logout") }}</button>
        </form>
    </p>
</div>

</body>
</html>
