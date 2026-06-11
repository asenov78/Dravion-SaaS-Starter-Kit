<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Dravion</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:#0a0a0b; font-family:Inter,system-ui,sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center;">

<div style="width:100%; max-width:380px; padding:0 16px;">
    <div style="text-align:center; margin-bottom:32px;">
        <div style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; background:#5e6ad2; border-radius:10px; margin-bottom:16px;">
            <span style="color:#fff; font-weight:700; font-size:18px;">D</span>
        </div>
        <h1 style="color:#e2e2e9; font-size:18px; font-weight:600; margin:0 0 4px;">Create an account</h1>
        <p style="color:#6b6b7b; font-size:13px; margin:0;">Get started with Dravion</p>
    </div>

    <x-ui.card>
        @if($errors->any())
        <x-ui.alert class="mb-4">{{ $errors->first() }}</x-ui.alert>
        @endif

        <form method="POST" action="/register">
            @csrf
            <div style="display:flex; flex-direction:column; gap:14px;">
                <x-ui.input name="name" label="Full Name" :value="old('name')" :error="$errors->first('name')" required autofocus />
                <x-ui.input name="email" type="email" label="Email" :value="old('email')" :error="$errors->first('email')" required />
                <x-ui.input name="password" type="password" label="Password" :error="$errors->first('password')" required />
                <x-ui.input name="password_confirmation" type="password" label="Confirm Password" required />
                <x-ui.button type="submit" style="width:100%;">Create account</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <p style="text-align:center; margin-top:16px; color:#6b6b7b; font-size:12px;">
        Already have an account?
        <a href="/login" style="color:#5e6ad2; text-decoration:none;"
            onmouseover="this.style.color='#7b84e0'" onmouseout="this.style.color='#5e6ad2'">Sign in</a>
    </p>
</div>

</body>
</html>
