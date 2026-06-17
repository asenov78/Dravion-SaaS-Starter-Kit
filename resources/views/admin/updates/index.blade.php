<x-layouts.admin :title="__('updates.title')">

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('updates.title') }}</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('updates.subtitle') }}</p>
</div>

{{-- Two-column layout --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- LEFT COLUMN: status + version --}}
    <div class="flex flex-col gap-4">

        {{-- License status card --}}
        <a href="{{ route('admin.license') }}"
            class="flex items-center justify-between rounded-2xl border bg-white px-5 py-4 transition-colors dark:bg-white/[0.03]
            {{ $licensed ? 'border-success-200 dark:border-success-800 hover:border-success-300' : 'border-warning-200 dark:border-warning-800 hover:border-warning-300' }}">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $licensed ? 'bg-success-50 dark:bg-success-500/10' : 'bg-warning-50 dark:bg-warning-500/10' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="{{ $licensed ? 'text-success-600 dark:text-success-400' : 'text-warning-600 dark:text-warning-400' }}">
                        @if($licensed)<path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>@else<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>@endif
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90 leading-tight">{{ __('license.title') }}</p>
                    <p class="text-xs mt-0.5 {{ $licensed ? 'text-success-600 dark:text-success-400' : 'text-warning-600 dark:text-warning-400' }}">
                        {{ $licensed ? __('license.licensed') : __('updates.license_required') }}
                    </p>
                </div>
            </div>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-400 shrink-0"><path d="M9 18l6-6-6-6"/></svg>
        </a>

        {{-- Version comparison card --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('updates.version_info') }}</p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                <div class="flex items-center justify-between px-5 py-3.5">
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('updates.current_version') }}</p>
                    <p class="text-sm font-semibold font-mono text-gray-800 dark:text-white/90">v{{ $current }}</p>
                </div>
                <div class="flex items-center justify-between px-5 py-3.5">
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('updates.latest_version') }}</p>
                    <p class="text-sm font-semibold font-mono {{ ($update['has_update'] ?? false) ? 'text-brand-600 dark:text-brand-400' : 'text-gray-800 dark:text-white/90' }}">
                        {{ $update['latest'] ? 'v' . $update['latest'] : '—' }}
                    </p>
                </div>
                <div class="flex items-center justify-between px-5 py-3.5">
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('updates.status') }}</p>
                    @if($update['latest'] === null)
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>{{ __('updates.unknown') }}
                        </span>
                    @elseif($update['has_update'])
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-xs font-medium rounded-full bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>{{ __('updates.update_available') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-xs font-medium rounded-full bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>{{ __('updates.up_to_date') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

    </div>{{-- /left --}}

    {{-- RIGHT COLUMN: main content --}}
    <div class="lg:col-span-2 flex flex-col gap-5">

        {{-- Update panel — fully driven by Alpine once page loads --}}
        <div id="update-panel"
            x-data="updateInstaller(
                @json($licensed),
                @json(array_reverse($update['newer'] ?? [])),
                @json($update['latest']),
                @json($current),
                @json($update['has_update'] ?? false),
                @json($update['latest'] === null)
            )">

            {{-- Cannot reach server --}}
            <template x-if="serverUnreachable">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6 flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-500"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                        </span>
                        <div>
                            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('updates.check_failed') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ __('updates.check_failed_desc') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.updates') }}" class="self-start inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                        {{ __('updates.check_again') }}
                    </a>
                </div>
            </template>

            {{-- Up to date --}}
            <template x-if="!serverUnreachable && !hasUpdate">
                <div class="rounded-2xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-6">
                    <div class="flex items-center gap-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-success-100 dark:bg-success-500/20">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-success-600 dark:text-success-400"><path d="M20 6L9 17l-5-5"/></svg>
                        </span>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('updates.up_to_date') }}</h3>
                            <p class="mt-0.5 text-sm text-gray-600 dark:text-gray-400" x-text="`v${installedVersion} — {{ __('updates.up_to_date_desc') }}`"></p>
                        </div>
                        <a href="{{ route('admin.updates') }}"
                            class="shrink-0 inline-flex items-center gap-2 rounded-lg border border-success-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-success-50 dark:border-success-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-success-500/10 transition-colors">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                            {{ __('updates.check_again') }}
                        </a>
                    </div>
                </div>
            </template>

            {{-- Update available — one version at a time --}}
            <template x-if="!serverUnreachable && hasUpdate">
                <div class="rounded-2xl border border-brand-200 bg-white dark:border-brand-500/30 dark:bg-gray-900">

                    {{-- Header --}}
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-brand-100 dark:border-brand-500/20">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-500/20">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-600 dark:text-brand-400"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('updates.update_available') }}</h3>
                                {{-- Badge: "2 of 5 pending" --}}
                                <template x-if="queue.length > 1">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-brand-100 text-brand-700 dark:bg-brand-500/20 dark:text-brand-300"
                                        x-text="`${pendingCount} {{ __('updates.pending') }}`"></span>
                                </template>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                                <span x-text="`v${installedVersion}`"></span>
                                <span class="mx-1">→</span>
                                <span class="font-semibold text-brand-600 dark:text-brand-400" x-text="`v${next.version}`"></span>
                                <template x-if="queue.length > 1">
                                    <span class="text-gray-400 dark:text-gray-500" x-text="` ({{ __('updates.step') }} ${currentStep} {{ __('updates.of') }} ${queue.length})`"></span>
                                </template>
                            </p>
                        </div>
                    </div>

                    {{-- Changelog for the NEXT version only --}}
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">{{ __('updates.whats_new') }}</p>

                        {{-- Upcoming versions (collapsed list) --}}
                        <template x-if="queue.length > 1">
                            <div class="mb-3 flex flex-wrap gap-1.5">
                                <template x-for="(rel, i) in queue" :key="rel.version">
                                    <span class="px-2 py-0.5 text-xs font-mono rounded-full border"
                                        :class="i === currentStep - 1
                                            ? 'bg-brand-50 border-brand-300 text-brand-700 dark:bg-brand-500/10 dark:border-brand-500/40 dark:text-brand-300 font-semibold'
                                            : i < currentStep - 1
                                                ? 'bg-success-50 border-success-200 text-success-700 dark:bg-success-500/10 dark:border-success-700 dark:text-success-400'
                                                : 'bg-gray-50 border-gray-200 text-gray-500 dark:bg-gray-800 dark:border-gray-700'"
                                        x-text="`v${rel.version}`"></span>
                                </template>
                            </div>
                        </template>

                        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-4 py-3 text-sm text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-wrap break-words overflow-hidden"
                            x-text="next.changelog || '—'"></div>
                    </div>

                    {{-- Actions --}}
                    <div class="px-6 py-4">
                        @if($licensed)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">{{ __('updates.install_warning') }}</p>
                            <div class="flex items-center gap-4 flex-wrap">
                                <button type="button" @click="install()" :disabled="loading"
                                    class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-60 transition-colors">
                                    <svg x-show="!loading" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    <svg x-show="loading" x-cloak class="animate-spin" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                                    <span x-text="loading
                                        ? '{{ __('updates.installing') }}'
                                        : (queue.length > 1 ? `{{ __('updates.install') }} v${next.version}` : '{{ __('updates.install') }}')">
                                    </span>
                                </button>
                                <p x-show="message" x-text="message" x-cloak
                                    :class="lastOk ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400'"
                                    class="text-sm font-medium"></p>
                            </div>
                        @else
                            <div class="flex items-start gap-3 rounded-xl border border-warning-200 bg-warning-50 dark:border-warning-800 dark:bg-warning-500/10 p-4">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-warning-600 dark:text-warning-400 mt-0.5 shrink-0"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ __('updates.locked_title') }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ __('updates.locked_desc') }}</p>
                                    <a href="{{ route('admin.license') }}"
                                        class="mt-3 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                                        {{ __('updates.go_to_license') }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </template>

        </div>{{-- /update-panel --}}

    </div>{{-- /right --}}

</div>

{{-- UPDATE HISTORY ACCORDION --}}
<div class="mt-6">
    <div x-data="{ open: false }" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <button type="button" @click="open = !open"
            class="w-full flex items-center justify-between px-6 py-4 text-left focus:outline-none group">
            <div class="flex items-center gap-3">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-500"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                </span>
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ __('updates.installed_versions') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ count($history) }} {{ __('updates.versions_installed') }}</p>
                </div>
            </div>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                class="text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''">
                <path d="M6 9l6 6 6-6"/>
            </svg>
        </button>
        <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-gray-800">
            @if(empty($history))
            <div class="px-6 py-6 text-center text-sm text-gray-400 dark:text-gray-500">
                {{ __('updates.no_history') }}
            </div>
            @else
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach(array_reverse($history) as $entry)
                <div x-data="{ expanded: false }" class="px-6 py-4">
                    <div class="flex items-start gap-3">
                        <span class="flex h-7 w-7 shrink-0 mt-0.5 items-center justify-center rounded-full bg-success-50 dark:bg-success-500/10">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-success-600 dark:text-success-400"><path d="M20 6L9 17l-5-5"/></svg>
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-semibold font-mono text-gray-800 dark:text-white/90">
                                    @if($entry['from'] === '—')
                                        v{{ $entry['to'] }}
                                    @else
                                        v{{ $entry['from'] }} → v{{ $entry['to'] }}
                                    @endif
                                </span>
                                <span class="px-1.5 py-0.5 text-[10px] font-semibold uppercase rounded bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-400 leading-none">
                                    {{ __('updates.installed') }}
                                </span>
                                @if(!empty($entry['changelog']))
                                <button type="button" @click="expanded = !expanded"
                                    class="text-xs text-brand-500 hover:text-brand-600 dark:text-brand-400 font-medium ml-auto">
                                    <span x-text="expanded ? '{{ __('updates.hide_changelog') }}' : '{{ __('updates.show_changelog') }}'"></span>
                                </button>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                {{ \Carbon\Carbon::parse($entry['installed_at'])->format('d M Y, H:i') }}
                            </p>
                            @if(!empty($entry['changelog']))
                            <div x-show="expanded" x-collapse class="mt-3">
                                <div class="rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-4 py-3">
                                    <div class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-wrap break-words overflow-hidden">{{ trim($entry['changelog']) }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function updateInstaller(licensed, queue, latestVersion, startVersion, hasUpdateInit, serverUnreachableInit) {
    return {
        licensed,
        // queue = older releases first, e.g. [1.10.21, 1.10.22, 1.10.23]
        queue,
        installedVersion: startVersion,
        latestVersion,
        hasUpdate: hasUpdateInit,
        serverUnreachable: serverUnreachableInit,
        currentStep: 1,       // 1-based index into queue
        loading: false,
        lastOk: null,
        message: '',

        get next() { return this.queue[this.currentStep - 1] || null; },
        get pendingCount() { return this.queue.length - (this.currentStep - 1); },

        async install() {
            if (!this.next || !this.licensed || this.loading) return;
            this.loading = true;
            this.message = '';
            this.lastOk  = null;

            try {
                const res  = await fetch('{{ route('admin.updates.install') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        zip_url:   this.next.zip_url,
                        changelog: this.next.changelog || '',
                    }),
                });
                const data = await res.json();
                this.loading = false;

                if (data.ok) {
                    this.lastOk          = true;
                    this.installedVersion = data.version || this.next.version;
                    const justInstalled  = this.next.version;
                    this.currentStep++;

                    if (this.currentStep > this.queue.length) {
                        // All versions installed — mark up-to-date
                        this.hasUpdate = false;
                        this.message   = '';
                    } else {
                        // More pending — show success for installed, prompt for next
                        this.message = `v${justInstalled} {{ __('updates.install_success') }} — {{ __('updates.next_ready') }} v${this.next.version}`;
                    }
                } else {
                    this.lastOk  = false;
                    this.message = data.message || '{{ __('updates.install_failed') }}';
                }
            } catch (e) {
                this.loading = false;
                this.lastOk  = false;
                this.message = e.message;
            }
        },
    };
}
</script>

</x-layouts.admin>
