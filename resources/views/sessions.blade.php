<x-layouts.portal :title="__('sessions.title')">

<div style="max-width:720px; margin:0 auto; padding:32px 16px;">

    <div style="margin-bottom:24px;">
        <h1 style="font-size:20px; font-weight:700; color:var(--color-gray-900); margin:0 0 4px;">{{ __('sessions.title') }}</h1>
        <p style="color:var(--color-gray-500); font-size:13px; margin:0;">{{ __('sessions.subtitle') }}</p>
    </div>

    @if(session('success'))
        <x-ui.alert variant="success" class="mb-5">{{ session('success') }}</x-ui.alert>
    @endif

    {{-- Active sessions list --}}
    @if($sessions->isNotEmpty())
    <x-ui.card class="mb-6">
        <h2 style="font-size:15px; font-weight:600; color:var(--color-gray-800); margin:0 0 16px;">{{ __('sessions.active') }}</h2>
        <div style="display:flex; flex-direction:column; gap:0;">
            @foreach($sessions as $session)
            <div style="display:flex; align-items:flex-start; gap:12px; padding:12px 0; border-bottom:1px solid var(--color-gray-100);">
                <div style="flex-shrink:0; margin-top:2px; color:{{ $session->is_current ? '#22c55e' : 'var(--color-gray-400)' }};">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                    </svg>
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <span style="font-size:13px; font-weight:500; color:var(--color-gray-800);">{{ $session->ip_address ?? __('sessions.unknown_ip') }}</span>
                        @if($session->is_current)
                            <span style="font-size:11px; background:#dcfce7; color:#15803d; padding:1px 8px; border-radius:999px; font-weight:600;">{{ __('sessions.this_device') }}</span>
                        @endif
                    </div>
                    <p style="font-size:11px; color:var(--color-gray-400); margin:2px 0 0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ Str::limit($session->user_agent ?? __('sessions.unknown_browser'), 80) }}
                    </p>
                    <p style="font-size:11px; color:var(--color-gray-400); margin:2px 0 0;">
                        {{ __('sessions.last_active') }}: {{ $session->last_activity->diffForHumans() }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </x-ui.card>
    @endif

    {{-- Logout other devices --}}
    <x-ui.card x-data="{open: false}">
        <h2 style="font-size:15px; font-weight:600; color:var(--color-gray-800); margin:0 0 8px;">{{ __('sessions.logout_others_title') }}</h2>
        <p style="font-size:13px; color:var(--color-gray-500); margin:0 0 16px;">{{ __('sessions.logout_others_desc') }}</p>

        <button @click="open = !open"
            style="padding:8px 16px; background:#ef4444; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:500; cursor:pointer;">
            {{ __('sessions.logout_others_btn') }}
        </button>

        <div x-show="open" x-cloak style="margin-top:16px; padding-top:16px; border-top:1px solid var(--color-gray-100);">
            <form method="POST" action="{{ route('sessions.logout-others') }}">
                @csrf
                <div style="display:flex; gap:10px; align-items:flex-start;">
                    <div style="flex:1;">
                        <x-ui.input name="password" type="password" :placeholder="__('auth.password_label')" />
                        @error('password')<p style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                    <x-ui.button type="submit" variant="danger">{{ __('app.confirm') }}</x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.card>

    <p style="margin-top:16px; font-size:12px; color:var(--color-gray-400);">
        <a href="{{ route('dashboard') }}" style="color:#5e6ad2; text-decoration:none;">← {{ __('app.back') }}</a>
    </p>

</div>
</x-layouts.portal>
