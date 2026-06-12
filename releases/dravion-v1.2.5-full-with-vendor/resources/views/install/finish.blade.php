<x-install.layout :steps="$steps" :current="$current">

<h2 style="color:#e2e2e9; font-size:16px; font-weight:700; margin:0 0 4px; letter-spacing:-0.01em;">Ready to Install</h2>
<p style="color:#4a5a7a; font-size:13px; margin:0 0 20px;">Everything looks good. Click Install to run migrations, create your admin account, and activate your license.</p>

{{-- Summary --}}
<div style="display:flex; flex-direction:column; gap:2px; margin-bottom:24px;">
    @foreach([
        ['icon' => 'M20 6 9 17l-5-5', 'color' => '#4ade80', 'bg' => 'rgba(74,222,128,0.08)', 'border' => 'rgba(74,222,128,0.15)', 'text' => 'Server requirements met'],
        ['icon' => 'M20 6 9 17l-5-5', 'color' => '#4ade80', 'bg' => 'rgba(74,222,128,0.08)', 'border' => 'rgba(74,222,128,0.15)', 'text' => session('install_db') ? 'Database connection verified' : 'Using existing database config'],
        ['icon' => 'M20 6 9 17l-5-5', 'color' => '#4ade80', 'bg' => 'rgba(74,222,128,0.08)', 'border' => 'rgba(74,222,128,0.15)', 'text' => 'Admin account configured: ' . (session('install_admin.email') ?? '—')],
        ['icon' => 'M20 6 9 17l-5-5', 'color' => '#4ade80', 'bg' => 'rgba(74,222,128,0.08)', 'border' => 'rgba(74,222,128,0.15)', 'text' => 'License key provided'],
    ] as $item)
    <div style="display:flex; align-items:center; gap:10px; padding:9px 12px; border-radius:7px; background:{{ $item['bg'] }}; border:1px solid {{ $item['border'] }};">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $item['color'] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $item['icon'] }}"/></svg>
        <span style="color:#8a9aba; font-size:12.5px;">{{ $item['text'] }}</span>
    </div>
    @endforeach
</div>

<div style="background:rgba(94,106,210,0.07); border:1px solid rgba(94,106,210,0.15); border-radius:8px; padding:12px 14px; margin-bottom:24px;">
    <p style="color:#a5b0f5; font-size:12px; margin:0; line-height:1.6;">
        <strong>What happens next:</strong> Migrations run, roles are seeded, admin user is created, and the installer is locked. This takes a few seconds.
    </p>
</div>

<form method="POST" action="{{ route('install.process', 'finish') }}">
    @csrf
    <button type="submit" style="width:100%; padding:10px; border-radius:8px; font-size:13px; font-weight:600; font-family:Inter,system-ui; cursor:pointer; border:none; background:linear-gradient(135deg,#5e6ad2,#818cf8); color:#fff; box-shadow:0 2px 8px rgba(94,106,210,0.35); transition:opacity 0.1s;"
        onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'"
        onclick="this.disabled=true; this.textContent='Installing…'; this.form.submit();">
        Install Dravion →
    </button>
</form>

</x-install.layout>
