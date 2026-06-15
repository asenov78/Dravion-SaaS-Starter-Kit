<x-layouts.admin :title="__('languages.title')">

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('languages.title') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('languages.subtitle') }}</p>
    </div>
</div>

@if($errors->any())
    <x-ui.alert variant="error" :message="$errors->first()" class="mb-6" />
@endif

{{-- Add language --}}
<div class="mb-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('languages.add') }}</h3>
    </div>
    <div class="p-6">
        <form method="POST" action="{{ route('admin.languages.store') }}" class="flex flex-wrap gap-3">
            @csrf
            <input type="text" name="code" placeholder="{{ __('languages.code_placeholder') }}" maxlength="10"
                class="h-11 w-28 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required />
            <input type="text" name="name" placeholder="{{ __('languages.name_placeholder') }}" maxlength="100"
                class="h-11 flex-1 min-w-[160px] rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required />
            <input type="text" name="flag" placeholder="{{ __('languages.flag_placeholder') }}" maxlength="10"
                class="h-11 w-36 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            <button type="submit"
                class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                {{ __('languages.add') }}
            </button>
        </form>
    </div>
</div>

{{-- Language list --}}
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('languages.installed') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('languages.language') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('languages.code') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('languages.translated') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('app.status') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @php $totalKeys = count(\App\Services\LangKeyExtractor::keys('en')); @endphp
                @forelse($languages as $lang)
                @php
                    $filled  = $lang->lines()->where('value', '!=', '')->count();
                    $total   = $lang->lines()->count();
                    $pct     = $total > 0 ? round($filled / $total * 100) : 0;
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-6 py-4">
                        <span class="text-xl mr-2">{{ $lang->flag }}</span>
                        <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $lang->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm font-mono text-gray-500 dark:text-gray-400">{{ $lang->code }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 max-w-[80px] bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $pct === 100 ? 'bg-success-500' : ($pct > 50 ? 'bg-warning-500' : 'bg-error-400') }}"
                                    style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $filled }}/{{ $total }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($lang->is_default)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>{{ __('languages.default') }}
                        </span>
                        @else
                        <form method="POST" action="{{ route('admin.languages.default', $lang) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs text-brand-500 hover:text-brand-600 font-medium">{{ __('languages.set_default') }}</button>
                        </form>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.languages.meta', $lang) }}"
                                class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium">{{ __('app.edit') }}</a>
                            <a href="{{ route('admin.languages.edit', $lang) }}"
                                class="text-sm text-brand-500 hover:text-brand-600 font-medium">{{ __('languages.translations') }}</a>

                            <form method="POST" action="{{ route('admin.languages.reseed', $lang) }}">
                                @csrf
                                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium">
                                    {{ __('languages.reseed') }}
                                </button>
                            </form>

                            @php $canDelete = \App\Models\Language::where('id', '!=', $lang->id)->exists(); @endphp
                            @if($canDelete)
                            <form method="POST" action="{{ route('admin.languages.destroy', $lang) }}"
                                onsubmit="return confirm('Delete {{ $lang->name }}?{{ $lang->is_default ? " Another language will become default." : "" }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm text-error-500 hover:text-error-600 font-medium">{{ __('app.delete') }}</button>
                            </form>
                            @else
                            <span class="text-xs text-gray-300 dark:text-gray-600">{{ __('languages.last') }}</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                        {{ __('languages.empty') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</x-layouts.admin>
