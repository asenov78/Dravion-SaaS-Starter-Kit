<x-layouts.admin :title="__('updates.title')">

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('updates.title') }}</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('updates.subtitle') }}</p>
</div>


{{-- Two-column layout --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- LEFT COLUMN: license + version --}}
    <div class="flex flex-col gap-4">

        {{-- License status card --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="text-brand-500" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('license.status') }}</h3>
                            @if($masked)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Key: <span class="font-mono text-brand-500">{{ $masked }}</span></p>
                            @else
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('license.no_key') }}</p>
                            @endif
                        </div>
                    </div>
                    @if($valid)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>{{ __('license.licensed') }}
                    </span>
                    @elseif($masked)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-error-500"></span>{{ __('license.invalid') }}
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-warning-500"></span>{{ __('license.no_license') }}
                    </span>
                    @endif
                </div>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.license.update') }}">
                    @csrf
                    <input type="hidden" name="_back" value="{{ route('admin.updates') }}">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('license.enter_key_desc') }}</p>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('license.enter_key') }}</label>
                        <input type="text" name="license_key"
                            placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                            value="{{ old('license_key') }}"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-mono text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800
                            {{ $errors->has('license_key') ? 'border-error-400 dark:border-error-600' : '' }}" />
                        @error('license_key')
                        <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                            class="inline-flex items-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                            {{ __('license.activate') }}
                        </button>

                        @if($masked)
                        <button type="button"
                            onclick="if(confirm('Remove license key? This installation will become unlicensed.')) document.getElementById('remove-license-form').submit()"
                            class="inline-flex items-center rounded-lg border border-error-200 bg-error-50 px-5 py-2.5 text-sm font-medium text-error-700 hover:bg-error-100 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400 dark:hover:bg-error-500/20 transition-colors">
                            {{ __('license.remove') }}
                        </button>
                        @endif
                    </div>
                </form>

                @if($masked)
                <form id="remove-license-form" method="POST" action="{{ route('admin.license.remove') }}" class="hidden">
                    @csrf @method('DELETE')
                    <input type="hidden" name="_back" value="{{ route('admin.updates') }}">
                </form>
                @endif
            </div>
        </div>

        {{-- License info note --}}
        <div class="flex gap-3 rounded-xl border border-brand-100 bg-brand-50 px-5 py-4 dark:border-brand-500/20 dark:bg-brand-500/5">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand-500 mt-0.5 flex-shrink-0">
                <circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>
            </svg>
            <p class="text-sm text-brand-700 dark:text-brand-300 leading-relaxed">
                {{ __('license.info') }}
            </p>
        </div>

        {{-- Version comparison card --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('updates.version_info') }}</p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                <div class="flex items-center justify-between px-5 py-3.5">
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('updates.current_version') }}</p>
                    <p class="text-sm font-semibold font-mono {{ $licensed ? 'text-gray-800 dark:text-white/90' : 'text-error-600 dark:text-error-400' }}" id="current-version-display">v{{ $current }}</p>
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
    <div class="flex flex-col gap-5">

        @if($update['latest'] === null)
            {{-- Cannot reach server --}}
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
                <form method="POST" action="{{ route('admin.updates.check-all') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                        {{ __('updates.check_again') }}
                    </button>
                </form>
            </div>

        @elseif($update['has_update'])
            {{-- Update available — one version per button click, page reloads after each install --}}
            <div @if($licensed) x-data="updateInstaller()" @endif
                class="rounded-2xl border border-brand-200 bg-white dark:border-brand-500/30 dark:bg-gray-900">

                {{-- Main update panel --}}
                <div>

                    {{-- Header --}}
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-brand-100 dark:border-brand-500/20">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-500/20">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-600 dark:text-brand-400"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('updates.update_available') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">v{{ $current }} → <span class="font-semibold text-brand-600 dark:text-brand-400">v{{ $update['latest'] }}</span></p>
                        </div>
                    </div>

                    {{-- Next version to install (oldest pending, one at a time) --}}
                    @if(!empty($update['newer']))
                    @php $nextVersion = end($update['newer']); @endphp
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">{{ __('updates.whats_new') }}</p>
                        <div class="py-2">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm font-semibold font-mono text-gray-800 dark:text-white/90">v{{ $nextVersion['version'] }}</span>
                                @if(count($update['newer']) > 1)
                                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                    +{{ count($update['newer']) - 1 }} {{ __('updates.more_pending') }}
                                </span>
                                @endif
                            </div>
                            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-4 py-3 text-sm text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-wrap break-words overflow-hidden">{{ trim($nextVersion['changelog']) ?: '—' }}</div>
                        </div>
                    </div>
                    @endif

                    {{-- Actions --}}
                    <div class="px-6 py-4">
                        @if($licensed)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">{{ __('updates.install_warning') }}</p>
                            <div class="flex items-center gap-4 flex-wrap">
                                @if(!empty($update['newer']))
                                @php $nextVersion = $nextVersion ?? end($update['newer']); @endphp
                                <button type="button" @click="install()" :disabled="loading"
                                    class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-60 transition-colors"
                                    data-install-btn="1">
                                    <svg x-show="!loading" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    <svg x-show="loading" class="animate-spin" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                                    <span x-text="loading ? currentMessage : '{{ __('updates.install') }} v{{ $nextVersion['version'] }}'"></span>
                                </button>
                                @endif
                                <p x-show="message && !loading" x-text="message" x-cloak
                                    :class="ok ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400'"
                                    class="text-sm"></p>
                            </div>
                        @else
                            <div class="flex items-start gap-3 rounded-xl border border-warning-200 bg-warning-50 dark:border-warning-800 dark:bg-warning-500/10 p-4">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-warning-600 dark:text-warning-400 mt-0.5 shrink-0"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ __('updates.locked_title') }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ __('updates.locked_desc') }}</p>
                                    <form method="POST" action="{{ route('admin.updates.check-license') }}" class="mt-3">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                                            {{ __('updates.check_license') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>{{-- /main update panel --}}

            </div>

        @else
            {{-- Up to date --}}
            @if($licensed)
            <div class="rounded-2xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-6">
                <div class="flex items-center gap-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-success-100 dark:bg-success-500/20">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-success-600 dark:text-success-400"><path d="M20 6L9 17l-5-5"/></svg>
                    </span>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('updates.up_to_date') }}</h3>
                        <p class="mt-0.5 text-sm text-gray-600 dark:text-gray-400">{{ __('updates.up_to_date_desc') }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.updates.check-all') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-success-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-success-50 dark:border-success-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-success-500/10 transition-colors">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                            {{ __('updates.check_again') }}
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="rounded-2xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50 p-6">
                <div class="flex items-center gap-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-400 dark:text-gray-500"><path d="M20 6L9 17l-5-5"/></svg>
                    </span>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('updates.up_to_date') }}</h3>
                        <p class="mt-0.5 text-sm text-gray-600 dark:text-gray-400">{{ __('updates.up_to_date_desc') }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.updates.check-all') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:bg-transparent dark:text-gray-400 dark:hover:bg-gray-700 transition-colors">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                            {{ __('updates.check_again') }}
                        </button>
                    </form>
                </div>
            </div>
            @endif
        @endif

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

@if($licensed && $update['has_update'])
<script>
function updateInstaller() {
    // RULE: one button press = exactly one version installed.
    // queue is sorted oldest-first; we always install queue[0].
    // After success the page reloads so the next pending version is shown.
    // NEVER loop through all versions — migrations must run in order and
    // each version's post-install state is the base for the next version.
    const queue = @json(array_reverse($update['newer'] ?? []));

    return {
        loading: false,
        ok: null,
        message: '',
        currentMessage: '',

        async install() {
            if (this.loading || !queue.length) return;

            const rel = queue[0];
            if (!rel.zip_url) return;

            this.loading = true;
            this.ok = null;
            this.message = '';
            this.currentMessage = `{{ __('updates.installing') }} v${rel.version}`;

            try {
                const res  = await fetch('{{ route('admin.updates.install') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        zip_url:   rel.zip_url,
                        changelog: rel.changelog || '',
                    }),
                });
                const data = await res.json();

                if (data.ok) {
                    // Reload so the next pending version (if any) is shown fresh.
                    window.location.reload();
                } else {
                    this.loading = false;
                    this.ok = false;
                    this.message = data.message || '{{ __('updates.install_failed') }}';
                }
            } catch (e) {
                this.loading = false;
                this.ok = false;
                this.message = e.message;
            }
        },
    };
}
</script>
@endif

</x-layouts.admin>
