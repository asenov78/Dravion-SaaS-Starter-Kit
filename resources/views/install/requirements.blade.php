<x-install.layout :steps="$steps" :current="$current">

<h2 style="color:#e2e2e9; font-size:16px; font-weight:700; margin:0 0 4px; letter-spacing:-0.01em;">Server Requirements</h2>
<p style="color:#4a5a7a; font-size:13px; margin:0 0 20px;">Verifying your server meets the minimum requirements.</p>

@if($errors->has('requirements'))
<div style="display:flex; align-items:center; gap:8px; background:rgba(248,113,113,0.08); border:1px solid rgba(248,113,113,0.2); border-radius:8px; padding:10px 14px; color:#f87171; font-size:12px; margin-bottom:16px;">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    {{ $errors->first('requirements') }}
</div>
@endif

<div style="display:flex; flex-direction:column; gap:2px; margin-bottom:24px;">
    @foreach($checks as $label => $pass)
    <div style="display:flex; align-items:center; justify-content:space-between; padding:9px 12px; border-radius:7px; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.04);">
        <span style="color:#8a9aba; font-size:13px;">{{ $label }}</span>
        @if($pass)
        <span style="display:flex; align-items:center; gap:5px; color:#4ade80; font-size:12px; font-weight:500;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Pass
        </span>
        @else
        <span style="display:flex; align-items:center; gap:5px; color:#f87171; font-size:12px; font-weight:500;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Fail
        </span>
        @endif
    </div>
    @endforeach
</div>

@php $allPass = !in_array(false, $checks, true); @endphp

<form method="POST" action="{{ route('install.process', 'requirements') }}">
    @csrf
    <button type="submit" {{ $allPass ? '' : 'disabled' }}
        style="width:100%; padding:10px; border-radius:8px; font-size:13px; font-weight:600; font-family:Inter,system-ui; cursor:{{ $allPass ? 'pointer' : 'not-allowed' }}; border:none; transition:opacity 0.1s;
        {{ $allPass ? 'background:linear-gradient(135deg,#5e6ad2,#818cf8); color:#fff; box-shadow:0 2px 8px rgba(94,106,210,0.35);' : 'background:rgba(255,255,255,0.05); color:#2a3a55;' }}">
        Continue →
    </button>
</form>

</x-install.layout>
