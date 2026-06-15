@php
    $isAdmin = auth()->user()?->hasAnyRole(['admin','manager','editor']);
    $backRoute = $isAdmin ? route('admin.dashboard') : route('dashboard');
@endphp

@if($isAdmin)
<x-layouts.admin :title="__('tokens.title')">
@else
<x-layouts.portal :title="__('tokens.title')">
@endif

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('tokens.title') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('tokens.subtitle') }}</p>
    </div>
    <a href="{{ $backRoute }}"
        class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        {{ __('app.back') }}
    </a>
</div>

{{-- New token notice --}}
@if($new_token)
<div class="rounded-2xl border border-success-200 bg-success-50 p-5 dark:border-success-500/20 dark:bg-success-500/10 mb-5"
    x-data="{copied: false}">
    <p class="text-sm font-semibold text-success-700 dark:text-success-400 mb-2">{{ __('tokens.new_token_notice') }}</p>
    <div class="flex items-center gap-3">
        <code class="flex-1 min-w-0 block rounded-lg bg-white dark:bg-gray-900 border border-success-200 dark:border-success-500/20 px-3 py-2 text-sm font-mono text-gray-800 dark:text-white break-all">{{ $new_token }}</code>
        <button type="button"
            @click="navigator.clipboard.writeText('{{ $new_token }}'); copied = true; setTimeout(() => copied = false, 2000)"
            class="shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-success-300 dark:border-success-500/30 bg-white dark:bg-gray-900 px-3 py-2 text-xs font-medium text-success-700 dark:text-success-400 hover:bg-success-50 dark:hover:bg-success-500/10 transition-colors">
            <svg x-show="!copied" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
            <svg x-show="copied" x-cloak width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <span x-text="copied ? '{{ __('tokens.copied') }}' : '{{ __('tokens.copy') }}'"></span>
        </button>
    </div>
</div>
@endif

{{-- Create token --}}
<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 mb-5">
    <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-4">{{ __('tokens.create') }}</h3>
    <form method="POST" action="{{ route('api-tokens.store') }}" class="flex gap-3 items-start">
        @csrf
        <div class="flex-1">
            <x-ui.input name="name" type="text" :placeholder="__('tokens.name_placeholder')" value="{{ old('name') }}" />
            @error('name')
                <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
            @enderror
        </div>
        <x-ui.button type="submit" variant="primary">{{ __('tokens.create_btn') }}</x-ui.button>
    </form>
</div>

{{-- Existing tokens --}}
<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white">{{ __('tokens.existing') }}</h3>
        @if($tokens->isNotEmpty())
        <form method="POST" action="{{ route('api-tokens.destroy-all') }}"
            onsubmit="return confirm('{{ __('tokens.revoke_all_confirm') }}')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="text-xs text-error-500 hover:text-error-700 dark:hover:text-error-400 font-medium transition-colors">
                {{ __('tokens.revoke_all') }}
            </button>
        </form>
        @endif
    </div>

    @if($tokens->isEmpty())
        <p class="text-sm text-gray-400 dark:text-gray-500 py-4 text-center">{{ __('tokens.none') }}</p>
    @else
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($tokens as $token)
            <div class="flex items-center gap-4 py-4">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $token->name }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        {{ __('tokens.created') }}: {{ $token->created_at->diffForHumans() }}
                        &bull;
                        @if($token->last_used_at)
                            {{ __('tokens.last_used') }}: {{ $token->last_used_at->diffForHumans() }}
                        @else
                            {{ __('tokens.never_used') }}
                        @endif
                    </p>
                </div>
                <form method="POST" action="{{ route('api-tokens.destroy', $token->id) }}"
                    onsubmit="return confirm('{{ __('tokens.revoke_confirm') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="text-xs text-error-500 hover:text-error-700 dark:hover:text-error-400 font-medium transition-colors shrink-0">
                        {{ __('tokens.revoke') }}
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    @endif
</div>

@if($isAdmin)
</x-layouts.admin>
@else
</x-layouts.portal>
@endif
