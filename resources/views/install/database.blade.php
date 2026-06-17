<x-install.layout :steps="$steps" :current="$current">

@if($confirm_needed > 0)

{{-- Phase 2: confirm drop --}}
<h2 style="color:#e2e2e9; font-size:16px; font-weight:700; margin:0 0 4px; letter-spacing:-0.01em;">Database Already Has Tables</h2>
<p style="color:#4a5a7a; font-size:13px; margin:0 0 20px;">Connection to <strong style="color:#a0aec0;">{{ $saved_db['db_name'] ?? '' }}</strong> was successful.</p>

<div style="display:flex; align-items:flex-start; gap:12px; background:rgba(251,191,36,0.08); border:1px solid rgba(251,191,36,0.3); border-radius:10px; padding:14px 16px; margin-bottom:20px;">
    <svg style="flex-shrink:0; margin-top:1px;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <div>
        <p style="color:#fbbf24; font-size:13px; font-weight:600; margin:0 0 6px;">{{ $confirm_needed }} existing table(s) detected</p>
        <p style="color:#9aa5b4; font-size:12px; margin:0; line-height:1.6;">Continuing will <strong style="color:#f87171;">drop all tables</strong> and reinstall from scratch. All existing data will be permanently lost.</p>
    </div>
</div>

@if($errors->any())
<div style="display:flex; align-items:center; gap:8px; background:rgba(248,113,113,0.08); border:1px solid rgba(248,113,113,0.2); border-radius:8px; padding:10px 14px; color:#f87171; font-size:12px; margin-bottom:16px;">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('install.process', 'database') }}">
    @csrf
    <input type="hidden" name="phase" value="confirm">

    <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; margin-bottom:20px;">
        <input type="checkbox" name="confirm_drop" value="1" style="margin-top:2px; width:15px; height:15px; accent-color:#f87171; cursor:pointer; flex-shrink:0;">
        <span style="color:#a0aec0; font-size:13px; line-height:1.5;">I understand — drop all existing tables and reinstall the database from scratch</span>
    </label>

    <div style="display:flex; gap:10px;">
        <a href="{{ route('install.step', 'database') }}?reset=1"
           style="flex:1; padding:10px; border-radius:8px; font-size:13px; font-weight:600; font-family:Inter,system-ui; cursor:pointer; border:1px solid rgba(255,255,255,0.08); background:rgba(255,255,255,0.04); color:#9aa5b4; text-align:center; text-decoration:none; display:block;">
            ← Change credentials
        </a>
        <button type="submit" style="flex:1; padding:10px; border-radius:8px; font-size:13px; font-weight:600; font-family:Inter,system-ui; cursor:pointer; border:none; background:linear-gradient(135deg,#ef4444,#f87171); color:#fff; box-shadow:0 2px 8px rgba(239,68,68,0.35);">
            Drop &amp; Reinstall →
        </button>
    </div>
</form>

@else

{{-- Phase 1: credentials form --}}
<h2 style="color:#e2e2e9; font-size:16px; font-weight:700; margin:0 0 4px; letter-spacing:-0.01em;">Site URL & Database</h2>
<p style="color:#4a5a7a; font-size:13px; margin:0 0 20px;">Confirm your site URL (auto-detected) and enter database credentials.</p>

@if($errors->any())
<div style="display:flex; align-items:center; gap:8px; background:rgba(248,113,113,0.08); border:1px solid rgba(248,113,113,0.2); border-radius:8px; padding:10px 14px; color:#f87171; font-size:12px; margin-bottom:16px;">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('install.process', 'database') }}">
    @csrf
    <div style="display:flex; flex-direction:column; gap:14px; margin-bottom:24px;">

        <x-ui.input name="app_name" label="Application Name" :value="old('app_name', 'Dravion')" :error="$errors->first('app_name')" required placeholder="e.g. My SaaS App" />

        <div>
            <x-ui.input name="app_url" label="Site URL" :value="old('app_url', $detected_url)" :error="$errors->first('app_url')" required />
            <p style="color:#2a3a55; font-size:11px; margin:5px 0 0 2px;">Auto-detected from your current request. Edit if wrong.</p>
        </div>

        <div style="height:1px; background:rgba(255,255,255,0.05);"></div>

        <div style="display:flex; gap:12px;">
            <div style="flex:1;">
                <x-ui.input name="db_host" label="Host" :value="old('db_host', '127.0.0.1')" :error="$errors->first('db_host')" required />
            </div>
            <div style="width:90px; flex-shrink:0;">
                <x-ui.input name="db_port" label="Port" :value="old('db_port', '3306')" required />
            </div>
        </div>

        <x-ui.input name="db_name" label="Database Name" :value="old('db_name')" :error="$errors->first('db_name')" required placeholder="e.g. dravion_db" />
        <x-ui.input name="db_user" label="Username" :value="old('db_user')" :error="$errors->first('db_user')" required />
        <x-ui.input name="db_password" label="Password" type="password" placeholder="Leave blank if no password" />

    </div>

    <button type="submit" style="width:100%; padding:10px; border-radius:8px; font-size:13px; font-weight:600; font-family:Inter,system-ui; cursor:pointer; border:none; background:linear-gradient(135deg,#5e6ad2,#818cf8); color:#fff; box-shadow:0 2px 8px rgba(94,106,210,0.35); transition:opacity 0.1s;"
        onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
        Test & Continue →
    </button>
</form>

@endif

</x-install.layout>
