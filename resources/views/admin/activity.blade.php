<x-layouts.admin :title="__('activity.title')">

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('activity.title') }}</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('activity.subtitle') }}</p>
</div>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
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
                    $eventColor = match($activity->event) {
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
                            {{ $activity->event ?? $activity->log_name }}
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
