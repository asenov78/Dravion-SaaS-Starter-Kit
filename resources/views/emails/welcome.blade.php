<!DOCTYPE html>
<html>
<body style="font-family:sans-serif;padding:32px;color:#374151;max-width:560px;margin:0 auto;">
    <h2 style="margin:0 0 12px;color:#111827;">Welcome to {{ config('app.name') }}!</h2>
    <p style="margin:0 0 16px;">Hi {{ $user->name }}, your account has been created.</p>
    <table style="width:100%;background:#f9fafb;border-radius:8px;padding:16px;border:1px solid #e5e7eb;margin-bottom:24px;">
        <tr><td style="color:#6b7280;padding:4px 0;">Email</td><td style="font-weight:600;">{{ $user->email }}</td></tr>
        <tr><td style="color:#6b7280;padding:4px 0;">Password</td><td style="font-weight:600;font-family:monospace;">{{ $plainPassword }}</td></tr>
    </table>
    <p style="margin:0 0 24px;color:#6b7280;font-size:14px;">Please change your password after logging in for the first time.</p>
    <a href="{{ config('app.url') }}/login"
        style="display:inline-block;background:#7c3aed;color:#fff;padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:600;">
        Log In
    </a>
</body>
</html>
