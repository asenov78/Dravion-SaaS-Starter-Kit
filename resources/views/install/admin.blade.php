<x-install.layout :steps="$steps" :current="$current">

<h2 style="color:#e2e2e9; font-size:16px; font-weight:700; margin:0 0 4px; letter-spacing:-0.01em;">Admin Account</h2>
<p style="color:#4a5a7a; font-size:13px; margin:0 0 20px;">Create the super-admin account. You'll use these credentials to sign in after installation.</p>

@if($errors->any())
<div style="display:flex; align-items:center; gap:8px; background:rgba(248,113,113,0.08); border:1px solid rgba(248,113,113,0.2); border-radius:8px; padding:10px 14px; color:#f87171; font-size:12px; margin-bottom:16px;">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('install.process', 'admin') }}">
    @csrf
    <div style="display:flex; flex-direction:column; gap:14px; margin-bottom:24px;">
        <x-ui.input name="name"                  label="Full Name"        :value="old('name')"  :error="$errors->first('name')"  required autofocus />
        <x-ui.input name="email"                 label="Email"            type="email" :value="old('email')" :error="$errors->first('email')" required />
        <x-ui.input name="password"              label="Password"         type="password" :error="$errors->first('password')" required placeholder="Min. 8 characters" />
        <x-ui.input name="password_confirmation" label="Confirm Password" type="password" required />
    </div>

    <button type="submit" style="width:100%; padding:10px; border-radius:8px; font-size:13px; font-weight:600; font-family:Inter,system-ui; cursor:pointer; border:none; background:linear-gradient(135deg,#5e6ad2,#818cf8); color:#fff; box-shadow:0 2px 8px rgba(94,106,210,0.35); transition:opacity 0.1s;"
        onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
        Continue →
    </button>
</form>

</x-install.layout>
