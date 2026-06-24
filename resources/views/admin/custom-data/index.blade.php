<x-layouts.admin :title="__('nav.custom_data')">

@php
$reorderCategoriesUrl = route('admin.custom-data.categories.reorder');
$reorderFieldsUrl     = route('admin.custom-data.fields.reorder');
$csrfToken            = csrf_token();

$inputClass  = "h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800";
$textareaClass = "w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800";
$selectClass = "h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800";
$labelClass  = "block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400";
@endphp

{{-- ── Page header ──────────────────────────────────────────────────────── --}}
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('nav.custom_data') }}</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $categories->count() }} {{ Str::lower(__('nav.custom_data')) }}</p>
    </div>

    {{-- Add Category modal --}}
    <x-ui.dialog :title="__('custom_data.add_category')">
        <x-slot:trigger>
            <x-ui.button variant="primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                {{ __('custom_data.add_category') }}
            </x-ui.button>
        </x-slot:trigger>

        <form method="POST" action="{{ route('admin.custom-data.categories.store') }}" class="mt-4 flex flex-col gap-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="{{ $labelClass }}">{{ __('custom_data.label_en') }}</label>
                    <input type="text" name="name_en" required class="{{ $inputClass }}" />
                </div>
                <div>
                    <label class="{{ $labelClass }}">{{ __('custom_data.label_bg') }}</label>
                    <input type="text" name="name_bg" required class="{{ $inputClass }}" />
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <x-ui.button type="button" variant="secondary" @click="open = false">{{ __('app.cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary">{{ __('app.save') }}</x-ui.button>
            </div>
        </form>
    </x-ui.dialog>
</div>

{{-- ── Category list ────────────────────────────────────────────────────── --}}
<div
    x-data="{
        catIds: {{ json_encode($categories->pluck('id')->toArray()) }},
        moveCatUp(idx) {
            if (idx === 0) return;
            [this.catIds[idx - 1], this.catIds[idx]] = [this.catIds[idx], this.catIds[idx - 1]];
            this.saveCatOrder();
        },
        moveCatDown(idx) {
            if (idx >= this.catIds.length - 1) return;
            [this.catIds[idx], this.catIds[idx + 1]] = [this.catIds[idx + 1], this.catIds[idx]];
            this.saveCatOrder();
        },
        saveCatOrder() {
            fetch('{{ $reorderCategoriesUrl }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ $csrfToken }}' },
                body: JSON.stringify({ ids: this.catIds })
            });
        },
        fieldIds: {},
        initFields(catId, ids) { this.fieldIds[catId] = ids; },
        moveFieldUp(catId, idx) {
            if (idx === 0) return;
            const arr = this.fieldIds[catId];
            [arr[idx - 1], arr[idx]] = [arr[idx], arr[idx - 1]];
            this.saveFieldOrder(catId);
        },
        moveFieldDown(catId, idx) {
            const arr = this.fieldIds[catId];
            if (idx >= arr.length - 1) return;
            [arr[idx], arr[idx + 1]] = [arr[idx + 1], arr[idx]];
            this.saveFieldOrder(catId);
        },
        saveFieldOrder(catId) {
            fetch('{{ $reorderFieldsUrl }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ $csrfToken }}' },
                body: JSON.stringify({ ids: this.fieldIds[catId] })
            });
        }
    }"
    class="flex flex-col gap-5">

    @forelse($categories as $catIdx => $category)
    <div
        x-show="catIds.includes({{ $category->id }})"
        :style="'order:' + catIds.indexOf({{ $category->id }})"
        x-init="initFields({{ $category->id }}, {{ json_encode($category->fields->pluck('id')->toArray()) }})"
        class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">

        {{-- ── Category header ── --}}
        <div class="flex flex-wrap items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">

            {{-- Reorder arrows --}}
            <div class="flex flex-col gap-0.5 shrink-0">
                <button type="button"
                    @click="moveCatUp(catIds.indexOf({{ $category->id }}))"
                    :disabled="catIds.indexOf({{ $category->id }}) === 0"
                    title="{{ __('app.move_up') }}"
                    class="flex h-6 w-6 items-center justify-center rounded text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 disabled:opacity-25 disabled:cursor-not-allowed dark:hover:bg-gray-800 dark:hover:text-gray-200">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 15l-6-6-6 6"/></svg>
                </button>
                <button type="button"
                    @click="moveCatDown(catIds.indexOf({{ $category->id }}))"
                    :disabled="catIds.indexOf({{ $category->id }}) >= catIds.length - 1"
                    title="{{ __('app.move_down') }}"
                    class="flex h-6 w-6 items-center justify-center rounded text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 disabled:opacity-25 disabled:cursor-not-allowed dark:hover:bg-gray-800 dark:hover:text-gray-200">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
            </div>

            {{-- Title + badges --}}
            <div class="flex flex-1 items-center gap-2 min-w-0">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $category->label() }}</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $category->name_en }} / {{ $category->name_bg }}</span>
                @if($category->is_system)
                    <x-ui.badge variant="accent">{{ __('custom_data.system') }}</x-ui.badge>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 shrink-0">

                {{-- Add Field --}}
                <x-ui.dialog :title="__('custom_data.add_field')">
                    <x-slot:trigger>
                        <x-ui.button variant="secondary" size="sm">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                            {{ __('custom_data.add_field') }}
                        </x-ui.button>
                    </x-slot:trigger>

                    <form method="POST" action="{{ route('admin.custom-data.fields.store') }}"
                          class="mt-4 flex flex-col gap-4" x-data="{ fieldType: 'text' }">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $category->id }}">

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="{{ $labelClass }}">{{ __('custom_data.label_en') }}</label>
                                <input type="text" name="label_en" required class="{{ $inputClass }}" />
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">{{ __('custom_data.label_bg') }}</label>
                                <input type="text" name="label_bg" required class="{{ $inputClass }}" />
                            </div>
                        </div>

                        <div>
                            <label class="{{ $labelClass }}">{{ __('custom_data.field_type') }}</label>
                            <select name="type" x-model="fieldType" class="{{ $selectClass }}">
                                <option value="text">text</option>
                                <option value="textarea">textarea</option>
                                <option value="select">select</option>
                                <option value="checkbox">checkbox</option>
                            </select>
                        </div>

                        <div x-show="fieldType === 'select' || fieldType === 'checkbox'" x-cloak class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="{{ $labelClass }}">{{ __('custom_data.options_en') }}</label>
                                <textarea name="options_en" rows="4" class="{{ $textareaClass }}"></textarea>
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">{{ __('custom_data.options_bg') }}</label>
                                <textarea name="options_bg" rows="4" class="{{ $textareaClass }}"></textarea>
                            </div>
                            <p class="col-span-2 -mt-1 text-xs text-gray-400">{{ __('custom_data.options_hint') }}</p>
                        </div>

                        <div class="flex items-center gap-5">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="is_required" value="1"
                                    class="h-4 w-4 rounded border border-gray-300 text-brand-500 dark:border-gray-700">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('custom_data.required') }}</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="is_visible" value="1" checked
                                    class="h-4 w-4 rounded border border-gray-300 text-brand-500 dark:border-gray-700">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('custom_data.visible') }}</span>
                            </label>
                        </div>

                        <div class="flex justify-end gap-2 pt-1">
                            <x-ui.button type="button" variant="secondary" @click="open = false">{{ __('app.cancel') }}</x-ui.button>
                            <x-ui.button type="submit" variant="primary">{{ __('app.save') }}</x-ui.button>
                        </div>
                    </form>
                </x-ui.dialog>

                @if(!$category->is_system)
                {{-- Edit Category --}}
                <x-ui.dialog :title="__('custom_data.edit_category')">
                    <x-slot:trigger>
                        <x-ui.button variant="ghost" size="sm">{{ __('app.edit') }}</x-ui.button>
                    </x-slot:trigger>

                    <form method="POST" action="{{ route('admin.custom-data.categories.update', $category) }}"
                          class="mt-4 flex flex-col gap-4">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="{{ $labelClass }}">{{ __('custom_data.label_en') }}</label>
                                <input type="text" name="name_en" value="{{ $category->name_en }}" required class="{{ $inputClass }}" />
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">{{ __('custom_data.label_bg') }}</label>
                                <input type="text" name="name_bg" value="{{ $category->name_bg }}" required class="{{ $inputClass }}" />
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 pt-1">
                            <x-ui.button type="button" variant="secondary" @click="open = false">{{ __('app.cancel') }}</x-ui.button>
                            <x-ui.button type="submit" variant="primary">{{ __('app.save') }}</x-ui.button>
                        </div>
                    </form>
                </x-ui.dialog>

                {{-- Delete Category --}}
                <x-ui.button variant="danger" size="sm" type="button"
                    @click="window.dispatchEvent(new CustomEvent('confirm-action', { detail: {
                        title: '{{ __('custom_data.delete_category') }}',
                        message: '{{ __('custom_data.delete_category_confirm') }}',
                        btnLabel: '{{ __('app.delete') }}',
                        btnColor: '#dc2626',
                        url: '{{ route('admin.custom-data.categories.destroy', $category) }}',
                        method: 'DELETE',
                        successAction: 'redirect',
                        targetId: '{{ route('admin.custom-data.index') }}',
                        toastMessage: '{{ addslashes(__('flash.custom_category_deleted')) }}',
                        toastVariant: 'success'
                    }}))">
                    {{ __('app.delete') }}
                </x-ui.button>
                @endif
            </div>
        </div>

        {{-- ── Fields table ── --}}
        @if($category->fields->isEmpty())
        <div class="px-6 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
            {{ __('custom_data.no_fields') }}
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                        <th class="w-10 px-3 py-3"></th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('custom_data.field_label') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('custom_data.field_type') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('custom_data.field_required') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('custom_data.field_visible') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($category->fields as $fIdx => $field)
                    <tr
                        x-show="fieldIds[{{ $category->id }}] && fieldIds[{{ $category->id }}].includes({{ $field->id }})"
                        :style="'order:' + (fieldIds[{{ $category->id }}] ? fieldIds[{{ $category->id }}].indexOf({{ $field->id }}) : {{ $fIdx }})"
                        class="group transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-800/50">

                        {{-- Reorder --}}
                        <td class="px-3 py-3">
                            <div class="flex flex-col gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button"
                                    @click="moveFieldUp({{ $category->id }}, fieldIds[{{ $category->id }}].indexOf({{ $field->id }}))"
                                    :disabled="fieldIds[{{ $category->id }}].indexOf({{ $field->id }}) === 0"
                                    class="flex h-5 w-5 items-center justify-center rounded text-gray-400 hover:bg-gray-100 hover:text-gray-600 disabled:opacity-25 disabled:cursor-not-allowed dark:hover:bg-gray-700 dark:hover:text-gray-200 transition-colors">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 15l-6-6-6 6"/></svg>
                                </button>
                                <button type="button"
                                    @click="moveFieldDown({{ $category->id }}, fieldIds[{{ $category->id }}].indexOf({{ $field->id }}))"
                                    :disabled="fieldIds[{{ $category->id }}].indexOf({{ $field->id }}) >= fieldIds[{{ $category->id }}].length - 1"
                                    class="flex h-5 w-5 items-center justify-center rounded text-gray-400 hover:bg-gray-100 hover:text-gray-600 disabled:opacity-25 disabled:cursor-not-allowed dark:hover:bg-gray-700 dark:hover:text-gray-200 transition-colors">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                                </button>
                            </div>
                        </td>

                        {{-- Label --}}
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $field->label() }}</span>
                                @if($field->is_system)
                                    <x-ui.badge variant="accent">{{ __('custom_data.system') }}</x-ui.badge>
                                @endif
                            </div>
                            <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">{{ $field->label_en }} / {{ $field->label_bg }}</div>
                        </td>

                        {{-- Type --}}
                        <td class="px-5 py-3">
                            <x-ui.badge>{{ $field->type }}</x-ui.badge>
                        </td>

                        {{-- Required --}}
                        <td class="px-5 py-3">
                            @if($field->is_required)
                                <x-ui.badge variant="success">{{ __('app.yes') }}</x-ui.badge>
                            @else
                                <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- Visible --}}
                        <td class="px-5 py-3">
                            @if($field->is_visible)
                                <x-ui.badge variant="success">{{ __('app.yes') }}</x-ui.badge>
                            @else
                                <x-ui.badge variant="danger">{{ __('app.no') }}</x-ui.badge>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Edit field --}}
                                <x-ui.dialog :title="__('custom_data.edit_field')">
                                    <x-slot:trigger>
                                        <x-ui.button variant="ghost" size="sm">{{ __('app.edit') }}</x-ui.button>
                                    </x-slot:trigger>

                                    <form method="POST" action="{{ route('admin.custom-data.fields.update', $field) }}"
                                          class="mt-4 flex flex-col gap-4">
                                        @csrf @method('PATCH')

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="{{ $labelClass }}">{{ __('custom_data.label_en') }}</label>
                                                <input type="text" name="label_en" value="{{ $field->label_en }}" required class="{{ $inputClass }}" />
                                            </div>
                                            <div>
                                                <label class="{{ $labelClass }}">{{ __('custom_data.label_bg') }}</label>
                                                <input type="text" name="label_bg" value="{{ $field->label_bg }}" required class="{{ $inputClass }}" />
                                            </div>
                                        </div>

                                        @if(in_array($field->type, ['select', 'checkbox']))
                                        @php
                                            $enOpts = implode("\n", array_column($field->options ?? [], 'en'));
                                            $bgOpts = implode("\n", array_column($field->options ?? [], 'bg'));
                                        @endphp
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="{{ $labelClass }}">{{ __('custom_data.options_en') }}</label>
                                                <textarea name="options_en" rows="4" class="{{ $textareaClass }}">{{ $enOpts }}</textarea>
                                            </div>
                                            <div>
                                                <label class="{{ $labelClass }}">{{ __('custom_data.options_bg') }}</label>
                                                <textarea name="options_bg" rows="4" class="{{ $textareaClass }}">{{ $bgOpts }}</textarea>
                                            </div>
                                            <p class="col-span-2 -mt-1 text-xs text-gray-400">{{ __('custom_data.options_hint') }}</p>
                                        </div>
                                        @endif

                                        <div class="flex items-center gap-5">
                                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                                <input type="checkbox" name="is_required" value="1" {{ $field->is_required ? 'checked' : '' }}
                                                    class="h-4 w-4 rounded border border-gray-300 text-brand-500 dark:border-gray-700">
                                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('custom_data.required') }}</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                                <input type="checkbox" name="is_visible" value="1" {{ $field->is_visible ? 'checked' : '' }}
                                                    class="h-4 w-4 rounded border border-gray-300 text-brand-500 dark:border-gray-700">
                                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('custom_data.visible') }}</span>
                                            </label>
                                        </div>

                                        <div class="flex justify-end gap-2 pt-1">
                                            <x-ui.button type="button" variant="secondary" @click="open = false">{{ __('app.cancel') }}</x-ui.button>
                                            <x-ui.button type="submit" variant="primary">{{ __('app.save') }}</x-ui.button>
                                        </div>
                                    </form>
                                </x-ui.dialog>

                                @if(!$field->is_system)
                                <x-ui.button variant="danger" size="sm" type="button"
                                    @click="window.dispatchEvent(new CustomEvent('confirm-action', { detail: {
                                        title: '{{ __('custom_data.delete_field') }}',
                                        message: '{{ __('custom_data.delete_field_confirm') }}',
                                        btnLabel: '{{ __('app.delete') }}',
                                        btnColor: '#dc2626',
                                        url: '{{ route('admin.custom-data.fields.destroy', $field) }}',
                                        method: 'DELETE',
                                        successAction: 'redirect',
                                        targetId: '{{ route('admin.custom-data.index') }}',
                                        toastMessage: '{{ addslashes(__('flash.custom_field_deleted')) }}',
                                        toastVariant: 'success'
                                    }}))">
                                    {{ __('app.delete') }}
                                </x-ui.button>
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

    @empty
    <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-16 text-center dark:border-gray-700 dark:bg-gray-900">
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('custom_data.no_categories') }}</p>
    </div>
    @endforelse

</div>

</x-layouts.admin>
