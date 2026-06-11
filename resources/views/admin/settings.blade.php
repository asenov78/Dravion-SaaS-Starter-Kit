<x-layouts.admin title="Settings">

@if(session('success'))
<x-ui.alert variant="success" style="margin-bottom:20px;">{{ session('success') }}</x-ui.alert>
@endif

@if(!empty($errors) && $errors->any())
<x-ui.alert style="margin-bottom:20px;">{{ $errors->first() }}</x-ui.alert>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}">
@csrf @method('PUT')

<div style="display:flex; flex-direction:column; gap:20px; max-width:640px;">

    {{-- General --}}
    <x-ui.card title="General">
        <div style="display:flex; flex-direction:column; gap:14px;">
            <x-ui.input name="app_name" label="Application Name" :value="$settings['app_name']" required />
            <x-ui.input name="app_url"  label="Application URL"  :value="$settings['app_url']"  type="url" required />
            <x-ui.switch name="registration" label="Allow public registration" :checked="$settings['registration'] === '1'" />
        </div>
    </x-ui.card>

    {{-- Email --}}
    <x-ui.card title="Email">
        <div style="display:flex; flex-direction:column; gap:14px;">
            <x-ui.input name="mail_from"      label="From Address" type="email" :value="$settings['mail_from']" />
            <x-ui.input name="mail_from_name" label="From Name"              :value="$settings['mail_from_name']" />
        </div>
    </x-ui.card>

    {{-- License --}}
    <x-ui.card title="License">
        <div style="display:flex; flex-direction:column; gap:10px;">
            <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #2a2a35;">
                <span style="color:#6b6b7b; font-size:12px;">License Key</span>
                <x-ui.badge variant="{{ config('dravion.license_key') ? 'success' : 'danger' }}">
                    {{ config('dravion.license_key') ? 'Active' : 'Not Set' }}
                </x-ui.badge>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #2a2a35;">
                <span style="color:#6b6b7b; font-size:12px;">Version</span>
                <span style="color:#e2e2e9; font-size:12px; font-family:ui-monospace,monospace;">v{{ config('dravion.version') }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0;">
                <span style="color:#6b6b7b; font-size:12px;">License Server</span>
                <span style="color:#9b9bab; font-size:12px;">{{ config('dravion.license_server') }}</span>
            </div>
        </div>
    </x-ui.card>

    <div style="display:flex; justify-content:flex-end;">
        <x-ui.button type="submit">Save Settings</x-ui.button>
    </div>

</div>
</form>

</x-layouts.admin>
