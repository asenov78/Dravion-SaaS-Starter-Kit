<x-layouts.portal :title="__('tokens.title')">

<div style="max-width:720px; margin:0 auto; padding:32px 16px;">

    <div style="margin-bottom:24px;">
        <h1 style="font-size:20px; font-weight:700; color:var(--color-gray-900); margin:0 0 4px;">{{ __('tokens.title') }}</h1>
        <p style="color:var(--color-gray-500); font-size:13px; margin:0;">{{ __('tokens.subtitle') }}</p>
    </div>

    @if(session('success'))
        <x-ui.alert variant="success" class="mb-5">{{ session('success') }}</x-ui.alert>
    @endif

    {{-- New token plaintext (shown once) --}}
    @if($new_token)
    <x-ui.alert variant="success" class="mb-5" x-data="{copied: false}">
        <p class="font-semibold mb-1">{{ __('tokens.new_token_notice') }}</p>
        <div style="display:flex; gap:8px; align-items:center; margin-top:8px;">
            <code style="flex:1; background:rgba(0,0,0,.08); border-radius:6px; padding:8px 12px; font-size:12px; word-break:break-all;">{{ $new_token }}</code>
            <button @click="navigator.clipboard.writeText('{{ $new_token }}'); copied=true; setTimeout(()=>copied=false,2000)"
                style="flex-shrink:0; padding:6px 12px; background:#5e6ad2; color:#fff; border:none; border-radius:6px; font-size:12px; cursor:pointer;">
                <span x-show="!copied">{{ __('tokens.copy') }}</span>
                <span x-show="copied" x-cloak>{{ __('tokens.copied') }}</span>
            </button>
        </div>
    </x-ui.alert>
    @endif

    {{-- Create token form --}}
    <x-ui.card class="mb-6">
        <h2 style="font-size:15px; font-weight:600; color:var(--color-gray-800); margin:0 0 16px;">{{ __('tokens.create') }}</h2>
        <form method="POST" action="{{ route('api-tokens.store') }}" style="display:flex; gap:10px; align-items:flex-start;">
            @csrf
            <div style="flex:1;">
                <x-ui.input name="name" :placeholder="__('tokens.name_placeholder')" :value="old('name')" autofocus />
                @error('name')<p style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <x-ui.button type="submit">{{ __('tokens.create_btn') }}</x-ui.button>
        </form>
    </x-ui.card>

    {{-- Token list --}}
    <x-ui.card>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 style="font-size:15px; font-weight:600; color:var(--color-gray-800); margin:0;">{{ __('tokens.existing') }}</h2>
            @if($tokens->count() > 1)
            <form method="POST" action="{{ route('api-tokens.destroy-all') }}">
                @csrf
                @method('DELETE')
                <button type="submit" style="font-size:12px; color:#ef4444; background:none; border:none; cursor:pointer;"
                    onclick="return confirm('{{ __('tokens.revoke_all_confirm') }}')">
                    {{ __('tokens.revoke_all') }}
                </button>
            </form>
            @endif
        </div>

        @if($tokens->isEmpty())
            <p style="color:var(--color-gray-400); font-size:13px; text-align:center; padding:24px 0;">{{ __('tokens.none') }}</p>
        @else
        <div style="display:flex; flex-direction:column; gap:0;">
            @foreach($tokens as $token)
            <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid var(--color-gray-100);"
                id="token-row-{{ $token->id }}">
                <div>
                    <p style="font-size:14px; font-weight:500; color:var(--color-gray-800); margin:0;">{{ $token->name }}</p>
                    <p style="font-size:11px; color:var(--color-gray-400); margin:2px 0 0;">
                        {{ __('tokens.created') }} {{ $token->created_at->diffForHumans() }}
                        @if($token->last_used_at)
                            · {{ __('tokens.last_used') }} {{ $token->last_used_at->diffForHumans() }}
                        @else
                            · {{ __('tokens.never_used') }}
                        @endif
                    </p>
                </div>
                <form method="POST" action="{{ route('api-tokens.destroy', $token->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="font-size:12px; color:#ef4444; background:none; border:none; cursor:pointer; padding:4px 8px;"
                        onclick="return confirm('{{ __('tokens.revoke_confirm') }}')">
                        {{ __('tokens.revoke') }}
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif
    </x-ui.card>

    <p style="margin-top:16px; font-size:12px; color:var(--color-gray-400);">
        <a href="{{ route('dashboard') }}" style="color:#5e6ad2; text-decoration:none;">← {{ __('app.back') }}</a>
    </p>

</div>
</x-layouts.portal>
