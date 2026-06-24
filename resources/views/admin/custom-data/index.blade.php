<x-layouts.admin :title="__('nav.custom_data')">

<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('nav.custom_data') }}</h2>
    </div>
    <button type="button"
        x-data
        @click="$dispatch('open-modal', 'add-category')"
        class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        {{ __('custom_data.add_category') }}
    </button>
</div>

@if (session('success'))
    <x-ui.alert variant="success" :message="session('success')" class="mb-6" />
@endif

@if($errors->any())
    <x-ui.alert variant="error" :message="$errors->first()" class="mb-6" />
@endif

<div class="flex flex-col gap-6">
    @forelse($categories as $category)
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        {{-- Category header --}}
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $category->label() }}</h3>
                @if($category->is_system)
                <span class="inline-flex items-center rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ __('custom_data.system') }}</span>
                @endif
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $category->name_en }} / {{ $category->name_bg }}</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button"
                    x-data
                    @click="$dispatch('open-modal', 'add-field-{{ $category->id }}')"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-brand-300 bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-700 hover:bg-brand-100 dark:border-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    {{ __('custom_data.add_field') }}
                </button>
                @if(!$category->is_system)
                <button type="button"
                    x-data
                    @click="$dispatch('open-modal', 'edit-category-{{ $category->id }}')"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300">
                    {{ __('app.edit') }}
                </button>
                <button type="button"
                    x-data
                    @click="$dispatch('confirm-action', { title: '{{ __('custom_data.delete_category') }}', message: '{{ __('custom_data.delete_category_confirm') }}', btnLabel: '{{ __('app.delete') }}', btnColor: '#dc2626', url: '{{ route('admin.custom-data.categories.destroy', $category) }}', method: 'DELETE', successAction: 'redirect', targetId: '{{ route('admin.custom-data.index') }}', toastMessage: '{{ addslashes(__('flash.custom_category_deleted')) }}', toastVariant: 'success' })"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-error-300 bg-error-50 px-3 py-1.5 text-xs font-medium text-error-700 hover:bg-error-100 dark:border-error-700 dark:bg-error-500/10 dark:text-error-400">
                    {{ __('app.delete') }}
                </button>
                @endif
            </div>
        </div>

        {{-- Fields table --}}
        @if($category->fields->isEmpty())
        <div class="px-6 py-8 text-center text-sm text-gray-400">{{ __('custom_data.no_fields') }}</div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        <th class="px-6 py-3">{{ __('custom_data.field_label') }}</th>
                        <th class="px-6 py-3">{{ __('custom_data.field_type') }}</th>
                        <th class="px-6 py-3">{{ __('custom_data.field_required') }}</th>
                        <th class="px-6 py-3">{{ __('custom_data.field_visible') }}</th>
                        <th class="px-6 py-3">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($category->fields as $field)
                    <tr class="text-sm text-gray-700 dark:text-gray-300">
                        <td class="px-6 py-3">
                            {{ $field->label() }}
                            @if($field->is_system)
                            <span class="ml-2 inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500 dark:bg-gray-800">{{ __('custom_data.system') }}</span>
                            @endif
                            <div class="text-xs text-gray-400">{{ $field->label_en }} / {{ $field->label_bg }}</div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $field->type }}</span>
                        </td>
                        <td class="px-6 py-3">
                            @if($field->is_required)
                            <span class="inline-flex items-center rounded-full bg-success-50 px-2 py-0.5 text-xs text-success-600 dark:bg-success-500/10 dark:text-success-400">{{ __('app.yes') }}</span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @if($field->is_visible)
                            <span class="inline-flex items-center rounded-full bg-success-50 px-2 py-0.5 text-xs text-success-600 dark:bg-success-500/10 dark:text-success-400">{{ __('app.yes') }}</span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500 dark:bg-gray-800">{{ __('app.no') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <button type="button"
                                    x-data
                                    @click="$dispatch('open-modal', 'edit-field-{{ $field->id }}')"
                                    class="text-xs text-brand-500 hover:underline">{{ __('app.edit') }}</button>
                                @if(!$field->is_system)
                                <button type="button"
                                    x-data
                                    @click="$dispatch('confirm-action', { title: '{{ __('custom_data.delete_field') }}', message: '{{ __('custom_data.delete_field_confirm') }}', btnLabel: '{{ __('app.delete') }}', btnColor: '#dc2626', url: '{{ route('admin.custom-data.fields.destroy', $field) }}', method: 'DELETE', successAction: 'redirect', targetId: '{{ route('admin.custom-data.index') }}', toastMessage: '{{ addslashes(__('flash.custom_field_deleted')) }}', toastVariant: 'success' })"
                                    class="text-xs text-error-500 hover:underline">{{ __('app.delete') }}</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Edit field modals for this category --}}
    @foreach($category->fields as $field)
    <x-ui.modal name="edit-field-{{ $field->id }}" :title="__('custom_data.edit_field')">
        <form method="POST" action="{{ route('admin.custom-data.fields.update', $field) }}">
            @csrf @method('PATCH')
            <div class="p-6 flex flex-col gap-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.label_en') }}</label>
                        <input type="text" name="label_en" value="{{ $field->label_en }}" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.label_bg') }}</label>
                        <input type="text" name="label_bg" value="{{ $field->label_bg }}" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                </div>
                @if($field->type === 'select')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.options') }}</label>
                    <textarea name="options" rows="4"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ implode("\n", $field->options ?? []) }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">{{ __('custom_data.options_hint') }}</p>
                </div>
                @endif
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_required" value="1" {{ $field->is_required ? 'checked' : '' }}
                            class="w-4 h-4 rounded border border-gray-300 text-brand-500 dark:border-gray-700">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('custom_data.required') }}</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_visible" value="1" {{ $field->is_visible ? 'checked' : '' }}
                            class="w-4 h-4 rounded border border-gray-300 text-brand-500 dark:border-gray-700">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('custom_data.visible') }}</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 pb-6">
                <button type="button" @click="$dispatch('close-modal')"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                    {{ __('app.cancel') }}
                </button>
                <button type="submit"
                    class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    {{ __('app.save') }}
                </button>
            </div>
        </form>
    </x-ui.modal>
    @endforeach

    {{-- Add field modal for this category --}}
    <x-ui.modal name="add-field-{{ $category->id }}" :title="__('custom_data.add_field')">
        <form method="POST" action="{{ route('admin.custom-data.fields.store') }}">
            @csrf
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            <div class="p-6 flex flex-col gap-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.label_en') }}</label>
                        <input type="text" name="label_en" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.label_bg') }}</label>
                        <input type="text" name="label_bg" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.field_type') }}</label>
                    <select name="type" x-data x-model="fieldType" x-init="fieldType = 'text'"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="text">text</option>
                        <option value="textarea">textarea</option>
                        <option value="select">select</option>
                        <option value="checkbox">checkbox</option>
                    </select>
                </div>
                <div x-show="fieldType === 'select'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.options') }}</label>
                    <textarea name="options" rows="4"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
                    <p class="mt-1 text-xs text-gray-400">{{ __('custom_data.options_hint') }}</p>
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_required" value="1"
                            class="w-4 h-4 rounded border border-gray-300 text-brand-500 dark:border-gray-700">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('custom_data.required') }}</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_visible" value="1" checked
                            class="w-4 h-4 rounded border border-gray-300 text-brand-500 dark:border-gray-700">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('custom_data.visible') }}</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 pb-6">
                <button type="button" @click="$dispatch('close-modal')"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                    {{ __('app.cancel') }}
                </button>
                <button type="submit"
                    class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    {{ __('app.save') }}
                </button>
            </div>
        </form>
    </x-ui.modal>

    {{-- Edit category modal --}}
    @if(!$category->is_system)
    <x-ui.modal name="edit-category-{{ $category->id }}" :title="__('custom_data.edit_category')">
        <form method="POST" action="{{ route('admin.custom-data.categories.update', $category) }}">
            @csrf @method('PUT')
            <div class="p-6 flex flex-col gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.label_en') }}</label>
                    <input type="text" name="name_en" value="{{ $category->name_en }}" required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.label_bg') }}</label>
                    <input type="text" name="name_bg" value="{{ $category->name_bg }}" required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 pb-6">
                <button type="button" @click="$dispatch('close-modal')"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                    {{ __('app.cancel') }}
                </button>
                <button type="submit"
                    class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    {{ __('app.save') }}
                </button>
            </div>
        </form>
    </x-ui.modal>
    @endif
    @empty
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 px-6 py-12 text-center">
        <p class="text-gray-500 dark:text-gray-400">{{ __('custom_data.no_categories') }}</p>
    </div>
    @endforelse
</div>

{{-- Add Category Modal --}}
<x-ui.modal name="add-category" :title="__('custom_data.add_category')">
    <form method="POST" action="{{ route('admin.custom-data.categories.store') }}">
        @csrf
        <div class="p-6 flex flex-col gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.label_en') }}</label>
                <input type="text" name="name_en" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.label_bg') }}</label>
                <input type="text" name="name_bg" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
        </div>
        <div class="flex justify-end gap-3 px-6 pb-6">
            <button type="button" @click="$dispatch('close-modal')"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                {{ __('app.cancel') }}
            </button>
            <button type="submit"
                class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                {{ __('app.save') }}
            </button>
        </div>
    </form>
</x-ui.modal>

</x-layouts.admin>
