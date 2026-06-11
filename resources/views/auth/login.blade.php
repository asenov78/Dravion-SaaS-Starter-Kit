<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in — Dravion</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:#0a0a0b; font-family:Inter,system-ui,sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center;">

<div style="width:100%; max-width:360px; padding:0 16px;">
    {{-- Logo --}}
    <div style="text-align:center; margin-bottom:32px;">
        <div style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; background:#5e6ad2; border-radius:10px; margin-bottom:16px;">
            <span style="color:#fff; font-weight:700; font-size:18px;">D</span>
        </div>
        <h1 style="color:#e2e2e9; font-size:18px; font-weight:600; margin:0 0 4px;">Sign in to Dravion</h1>
        <p style="color:#6b6b7b; font-size:13px; margin:0;">Enter your credentials to continue</p>
    </div>

    {{-- Card --}}
    <div style="background:#111113; border:1px solid #2a2a35; border-radius:12px; padding:24px;">

        @if($errors->any())
        <div style="background:#7f1d1d20; border:1px solid #7f1d1d50; border-radius:8px; padding:10px 14px; margin-bottom:16px; color:#f87171; font-size:13px;">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; color:#6b6b7b; font-size:12px; font-weight:500; margin-bottom:6px;">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    style="width:100%; padding:9px 12px; background:#0a0a0b; border:1px solid #2a2a35; border-radius:8px; color:#e2e2e9; font-size:14px; outline:none; box-sizing:border-box; font-family:Inter,system-ui;"
                    onfocus="this.style.borderColor='#5e6ad2'" onblur="this.style.borderColor='#2a2a35'">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; color:#6b6b7b; font-size:12px; font-weight:500; margin-bottom:6px;">Password</label>
                <input type="password" name="password" required
                    style="width:100%; padding:9px 12px; background:#0a0a0b; border:1px solid #2a2a35; border-radius:8px; color:#e2e2e9; font-size:14px; outline:none; box-sizing:border-box; font-family:Inter,system-ui;"
                    onfocus="this.style.borderColor='#5e6ad2'" onblur="this.style.borderColor='#2a2a35'">
            </div>

            <button type="submit"
                style="width:100%; padding:9px; background:#5e6ad2; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; font-family:Inter,system-ui;"
                onmouseover="this.style.background='#7b84e0'" onmouseout="this.style.background='#5e6ad2'">
                Sign in
            </button>
        </form>
    </div>

    <p style="text-align:center; margin-top:16px; color:#6b6b7b; font-size:12px;">
        Don't have an account?
        <a href="/register" style="color:#5e6ad2; text-decoration:none;"
            onmouseover="this.style.color='#7b84e0'" onmouseout="this.style.color='#5e6ad2'">Register</a>
    </p>
</div>

</body>
</html>
