<x-layouts.admin :title="__('languages.edit_title') . ': ' . $language->name">

<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">
            {{ $language->flag }} {{ $language->name }}
            <span class="ml-2 text-sm font-normal text-gray-400 dark:text-gray-500 font-mono">[{{ $language->code }}]</span>
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ __('languages.page_info', ['total' => $lines->total(), 'current' => $lines->currentPage(), 'last' => $lines->lastPage()]) }}
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.languages.index') }}"
            class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
            ← {{ __('languages.back') }}
        </a>
        <a href="{{ route('admin.languages.export', $language) }}"
            class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
            {{ __('languages.export_json') }}
        </a>
    </div>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('admin.languages.edit', $language) }}" class="mb-4 flex gap-3">
    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('languages.search_placeholder') }}"
        class="h-10 flex-1 max-w-sm rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
    <button type="submit"
        class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
        {{ __('app.search') }}
    </button>
    @if($search)
    <a href="{{ route('admin.languages.edit', $language) }}"
        class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors">
        {{ __('app.cancel') }}
    </a>
    @endif
</form>

<form method="POST" action="{{ route('admin.languages.batch', $language) }}">
    @csrf @method('PUT')

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('languages.translations') }}</h3>
            <button type="submit"
                class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                {{ __('languages.save_page') }}
            </button>
        </div>

        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 w-[35%]">{{ __('languages.key') }}</th>
                    @if($language->code !== 'en')
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 w-[30%]">{{ __('languages.english') }}</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ $language->name }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($lines as $line)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors {{ $line->value === '' ? 'bg-warning-50/30 dark:bg-warning-900/10' : '' }}">
                    <td class="px-6 py-3">
                        <span class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ $line->key }}</span>
                    </td>
                    @if($language->code !== 'en')
                    <td class="px-6 py-3 text-sm text-gray-400 dark:text-gray-500 italic">
                        {{ $enValues[$line->key] ?? '' }}
                    </td>
                    @endif
                    <td class="px-6 py-3">
                        <input type="text"
                            name="lines[{{ $line->key }}]"
                            value="{{ old('lines.' . $line->key, $line->value) }}"
                            placeholder="{{ $enValues[$line->key] ?? $line->key }}"
                            class="w-full rounded-lg border {{ $line->value === '' ? 'border-warning-300 dark:border-warning-700' : 'border-gray-300 dark:border-gray-700' }} bg-transparent px-3 py-1.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:text-white/90 dark:bg-gray-900" />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                        {{ __('languages.no_keys') }} <a href="{{ route('admin.languages.reseed', $language) }}" class="text-brand-500 underline" onclick="event.preventDefault();document.getElementById('reseed-form').submit()">{{ __('languages.reseed') }}</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($lines->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('languages.showing', ['from' => $lines->firstItem(), 'to' => $lines->lastItem(), 'total' => $lines->total()]) }}
            </div>
            <div class="flex items-center gap-1">
                @if($lines->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-400 rounded-lg border border-gray-200 dark:border-gray-700">←</span>
                @else
                <a href="{{ $lines->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-600 rounded-lg border border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">←</a>
                @endif

                @foreach($lines->getUrlRange(max(1, $lines->currentPage()-2), min($lines->lastPage(), $lines->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}"
                    class="px-3 py-1.5 text-sm rounded-lg border transition-colors {{ $page === $lines->currentPage() ? 'bg-brand-500 text-white border-brand-500' : 'border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                    {{ $page }}
                </a>
                @endforeach

                @if($lines->hasMorePages())
                <a href="{{ $lines->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-600 rounded-lg border border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">→</a>
                @else
                <span class="px-3 py-1.5 text-sm text-gray-400 rounded-lg border border-gray-200 dark:border-gray-700">→</span>
                @endif
            </div>
        </div>
        @endif

        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex justify-end">
            <button type="submit"
                class="inline-flex items-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                {{ __('languages.save_page') }}
            </button>
        </div>
    </div>
</form>

<form id="reseed-form" method="POST" action="{{ route('admin.languages.reseed', $language) }}" class="hidden">@csrf</form>

</x-layouts.admin>
