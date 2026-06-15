<x-layouts.admin :title="__('updates.title')">

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('updates.title') }}</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('updates.subtitle') }}</p>
</div>

<div class="max-w-2xl flex flex-col gap-5">

    {{-- License status — links updates & license together --}}
    <a href="{{ route('admin.license') }}"
        class="flex items-center justify-between rounded-2xl border bg-white px-6 py-4 transition-colors dark:bg-white/[0.03]
        {{ $licensed ? 'border-success-200 dark:border-success-800 hover:border-success-300' : 'border-warning-200 dark:border-warning-800 hover:border-warning-300' }}">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $licensed ? 'bg-success-50 dark:bg-success-500/10' : 'bg-warning-50 dark:bg-warning-500/10' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="{{ $licensed ? 'text-success-600 dark:text-success-400' : 'text-warning-600 dark:text-warning-400' }}">
                    @if($licensed)<path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>@else<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>@endif
                </svg>
            </span>
            <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ __('license.title') }}</p>
                <p class="text-xs {{ $licensed ? 'text-success-600 dark:text-success-400' : 'text-warning-600 dark:text-warning-400' }}">
                    {{ $licensed ? __('license.licensed') : __('updates.license_required') }}
                </p>
            </div>
        </div>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-400"><path d="M9 18l6-6-6-6"/></svg>
    </a>

    {{-- Version summary --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="grid grid-cols-2 divide-x divide-gray-100 dark:divide-gray-800">
            <div class="px-6 py-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('updates.current_version') }}</p>
                <p class="text-lg font-semibold font-mono text-gray-800 dark:text-white/90">v{{ $current }}</p>
            </div>
            <div class="px-6 py-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('updates.latest_version') }}</p>
                <p class="text-lg font-semibold font-mono {{ ($update['has_update'] ?? false) ? 'text-brand-600 dark:text-brand-400' : 'text-gray-800 dark:text-white/90' }}">
                    {{ $update['latest'] ? 'v' . $update['latest'] : '—' }}
                </p>
            </div>
        </div>
    </div>

    @if($update['latest'] === null)
        {{-- Could not reach update server --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('updates.check_failed') }}</p>
            <a href="{{ route('admin.updates') }}" class="mt-3 inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">{{ __('updates.check_again') }}</a>
        </div>

    @elseif($update['has_update'])
        {{-- Update available --}}
        <div @if($licensed) x-data="updateInstaller()" @endif
            class="rounded-2xl border border-brand-200 bg-brand-50 dark:border-brand-500/30 dark:bg-brand-500/10 p-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-500/20">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-600 dark:text-brand-400"><path d="M21 12a9 9 0 11-6.219-8.56"/><polyline points="21 3 21 9 15 9"/></svg>
                </span>
                <div>
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('updates.update_available') }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">v{{ $current }} → <span class="font-semibold text-brand-600 dark:text-brand-400">v{{ $update['latest'] }}</span></p>
                </div>
            </div>

            @if(!empty($update['newer']))
            <div class="rounded-lg bg-white/60 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-800 p-4 mb-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">{{ __('updates.whats_new') }}</p>
                <div class="flex flex-col divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach($update['newer'] as $rel)
                    <div class="py-3 first:pt-0 last:pb-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-sm font-semibold font-mono text-gray-800 dark:text-white/90">v{{ $rel['version'] }}</span>
                            @if($rel['version'] === $update['latest'])
                            <span class="px-1.5 py-0.5 text-[10px] font-semibold uppercase rounded bg-brand-500 text-white">{{ __('updates.latest_version') }}</span>
                            @endif
                        </div>
                        <pre class="whitespace-pre-wrap text-sm text-gray-600 dark:text-gray-300 font-sans">{{ trim($rel['changelog']) ?: '—' }}</pre>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($licensed)
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">{{ __('updates.install_warning') }}</p>
                <div class="flex items-center gap-4">
                    <button type="button" @click="install()" :disabled="loading"
                        class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-60 transition-colors">
                        <svg x-show="!loading" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <svg x-show="loading" class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                        <span x-text="loading ? '{{ __('updates.installing') }}' : '{{ __('updates.install') }}'"></span>
                    </button>
                    <p x-show="message" x-text="message" x-cloak
                        :class="ok ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400'"
                        class="text-sm"></p>
                </div>
            @else
                {{-- Update exists but locked behind a license --}}
                <div class="rounded-lg border border-warning-200 bg-warning-50 dark:border-warning-800 dark:bg-warning-500/10 p-4 flex items-start gap-3">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-warning-600 dark:text-warning-400 mt-0.5 shrink-0"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ __('updates.locked_title') }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ __('updates.locked_desc') }}</p>
                        <a href="{{ route('admin.license') }}"
                            class="mt-3 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                            {{ __('updates.go_to_license') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>

    @else
        {{-- Up to date --}}
        <div class="rounded-2xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-6">
            <div class="flex items-center gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-success-100 dark:bg-success-500/20">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-success-600 dark:text-success-400"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('updates.up_to_date') }}</h3>
                    <p class="mt-0.5 text-sm text-gray-600 dark:text-gray-400">{{ __('updates.up_to_date_desc') }}</p>
                </div>
                <a href="{{ route('admin.updates') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                    {{ __('updates.check_again') }}
                </a>
            </div>
        </div>
    @endif
</div>

@if($licensed && $update['has_update'] && $update['zip_url'])
<script>
    function updateInstaller() {
        return {
            loading: false,
            ok: null,
            message: '',
            install() {
                this.loading = true;
                this.message = '';
                fetch('{{ route('admin.updates.install') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ zip_url: @json($update['zip_url']) }),
                })
                .then(r => r.json().then(d => ({ status: r.status, body: d })))
                .then(({ status, body }) => {
                    this.loading = false;
                    this.ok = body.ok === true;
                    this.message = this.ok ? '{{ __('updates.install_success') }}' : (body.message || '{{ __('updates.install_failed') }}');
                    if (this.ok) setTimeout(() => window.location.reload(), 2000);
                })
                .catch(e => {
                    this.loading = false;
                    this.ok = false;
                    this.message = e.message;
                });
            },
        };
    }
</script>
@endif

</x-layouts.admin>
