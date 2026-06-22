<x-layouts.admin :title="__('activity.title')">

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('activity.title') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('activity.subtitle') }}</p>
    </div>
    <a href="{{ route('admin.activity.export', request()->only('search','log_name','causer_id','date_from','date_to')) }}"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        {{ __('activity.export_csv') }}
    </a>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.activity') }}" class="mb-4 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('activity.search_placeholder') }}"
        class="h-10 flex-1 min-w-48 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />

    <select name="log_name" onchange="this.form.submit()"
        class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        <option value="">{{ __('activity.filter_event') }}</option>
        @foreach($logNames as $ln)
        <option value="{{ $ln }}" {{ $logName === $ln ? 'selected' : '' }}>{{ $ln }}</option>
        @endforeach
    </select>

    <select name="causer_id" onchange="this.form.submit()"
        class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        <option value="">{{ __('activity.filter_user') }}</option>
        @foreach($users as $u)
        <option value="{{ $u->id }}" {{ $causerId == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
        @endforeach
    </select>

    <div x-data="{ fp: null, init() { this.fp = flatpickr(this.$refs.el, { dateFormat: 'Y-m-d', locale: window.fpConfig ?? { firstDayOfWeek: 1 }, defaultDate: '{{ $dateFrom }}' || null }); }, destroy() { this.fp?.destroy(); } }" x-init="init()" x-destroy="destroy()">
        <input x-ref="el" type="text" name="date_from" placeholder="{{ __('activity.filter_date_from') }}" autocomplete="off"
            class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
    </div>

    <div x-data="{ fp: null, init() { this.fp = flatpickr(this.$refs.el, { dateFormat: 'Y-m-d', locale: window.fpConfig ?? { firstDayOfWeek: 1 }, defaultDate: '{{ $dateTo }}' || null }); }, destroy() { this.fp?.destroy(); } }" x-init="init()" x-destroy="destroy()">
        <input x-ref="el" type="text" name="date_to" placeholder="{{ __('activity.filter_date_to') }}" autocomplete="off"
            class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
    </div>

    <button type="submit" class="h-10 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
        {{ __('app.search') }}
    </button>

    @if($search || $logName || $causerId || $dateFrom || $dateTo)
    <a href="{{ route('admin.activity') }}"
        class="h-10 inline-flex items-center rounded-lg border border-gray-300 px-4 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors">
        {{ __('app.cancel') }}
    </a>
    @endif
</form>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    @if($activities->isEmpty())
    <div class="px-6 py-16 text-center text-sm text-gray-400 dark:text-gray-500">
        {{ __('activity.empty') }}
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('activity.event') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('activity.description') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('activity.user') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('activity.subject') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('activity.when') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($activities as $activity)
                @php
                    $eventRaw = $activity->event ?? $activity->log_name;
                    $eventTransKey = 'activity.events.' . $eventRaw;
                    $eventLabel = __($eventTransKey) !== $eventTransKey ? __($eventTransKey) : $eventRaw;
                    $eventColor = match($eventRaw) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'error',
                        default   => 'default',
                    };
                    $eventClasses = match($eventColor) {
                        'success' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                        'warning' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
                        'error'   => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
                        default   => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                    };
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-6 py-3">
                        <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full {{ $eventClasses }}">
                            {{ $eventLabel }}
                        </span>
                    </td>
                    @php
                        $descKey    = $activity->getExtraProperty('desc_key');
                        $descParams = $activity->getExtraProperty('desc_params', []);
                        $displayDesc = $descKey ? __($descKey, $descParams) : $activity->description;
                    @endphp
                    <td class="px-6 py-3 max-w-xs">
                        <span class="block truncate text-sm text-gray-700 dark:text-gray-300" title="{{ $displayDesc }}">
                            {{ $displayDesc }}
                        </span>
                    </td>
                    <td class="px-6 py-3">
                        @if($activity->causer)
                        <div class="flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-brand-500 text-white text-xs font-semibold flex-shrink-0">
                                {{ strtoupper(substr($activity->causer->name, 0, 1)) }}
                            </span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $activity->causer->name }}</span>
                        </div>
                        @else
                        <span class="text-sm text-gray-400 dark:text-gray-500">{{ __('activity.system') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ $activity->subject_type ? class_basename($activity->subject_type) . ' #' . $activity->subject_id : '—' }}
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400" title="{{ $activity->created_at->format('Y-m-d H:i:s') }}">
                        {{ $activity->created_at->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($activities->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
        {{ $activities->links() }}
    </div>
    @endif
    @endif
</div>

</x-layouts.admin>
