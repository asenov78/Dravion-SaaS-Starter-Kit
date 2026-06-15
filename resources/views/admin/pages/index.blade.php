<x-layouts.admin :title="__('pages.title')">

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('pages.title') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('pages.subtitle') }}</p>
    </div>
    <a href="{{ route('admin.pages.create') }}"
        class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        {{ __('pages.add') }}
    </a>
</div>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('app.name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('pages.slug') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('pages.in_nav') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('pages.published') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('pages.sort') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($pages as $page)
                <tr id="row-{{ $page->id }}" class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-6 py-3">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $page->title }}</p>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $page->slug }}</td>
                    <td class="px-6 py-3">
                        @if($page->show_in_nav)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>{{ __('app.yes') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>{{ __('app.no') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        @if($page->is_published)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>{{ __('app.yes') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>{{ __('app.no') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $page->sort_order ?? '—' }}</td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('admin.pages.edit', $page) }}"
                                class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                {{ __('app.edit') }}
                            </a>
                            <button type="button"
                                @click="window.dispatchEvent(new CustomEvent('confirm-action', { detail: {
                                    title: '{{ addslashes(__('app.delete')) }} {{ addslashes($page->title) }}?',
                                    message: '{{ addslashes(__('app.delete')) }} &quot;{{ addslashes($page->title) }}&quot;?',
                                    btnLabel: '{{ addslashes(__('app.delete')) }}',
                                    btnColor: '#dc2626',
                                    url: '{{ route('admin.pages.destroy', $page) }}',
                                    method: 'DELETE',
                                    successAction: 'remove',
                                    targetId: 'row-{{ $page->id }}',
                                    toastMessage: '{{ addslashes(__('flash.page_deleted')) }}',
                                    toastVariant: 'error'
                                }}))"
                                class="inline-flex items-center rounded-lg border border-error-300 bg-error-100 px-3 py-1.5 text-xs font-medium text-error-800 hover:bg-error-200 dark:border-error-700 dark:bg-error-500/20 dark:text-error-300">
                                {{ __('app.delete') }}
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                        {{ __('pages.no_pages') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pages->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
        {{ $pages->links() }}
    </div>
    @endif
</div>

</x-layouts.admin>