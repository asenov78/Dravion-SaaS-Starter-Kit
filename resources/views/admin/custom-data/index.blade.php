<x-layouts.admin :title="__('nav.custom_data')">

@php
$reorderCategoriesUrl = route('admin.custom-data.categories.reorder');
$reorderFieldsUrl     = route('admin.custom-data.fields.reorder');
$csrfToken            = csrf_token();
@endphp

{{-- ── Page header ──────────────────────────────────────────────────────── --}}
<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('nav.custom_data') }}</h2>

    {{-- Add Category — inline modal --}}
    <div x-data="{ open: false }">
        <x-ui.button variant="primary" type="button" @click="open = true">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            {{ __('custom_data.add_category') }}
        </x-ui.button>

        <div x-show="open" x-cloak @keydown.escape.window="open = false"
            class="fixed inset-0 z-99999 flex items-center justify-center p-5">
            <div @click="open = false" class="absolute inset-0 bg-gray-400/50 backdrop-blur-[32px]"></div>
            <div @click.stop class="relative w-full max-w-md rounded-3xl bg-white dark:bg-gray-900 shadow-xl">
                <button @click="open = false"
                    class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                <div class="px-6 pt-6 pb-2 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('custom_data.add_category') }}</h3>
                </div>
                <form method="POST" action="{{ route('admin.custom-data.categories.store') }}">
                    @csrf
                    <div class="p-6 flex flex-col gap-4">
                        <x-ui.input name="name_en" :label="__('custom_data.label_en')" required />
                        <x-ui.input name="name_bg" :label="__('custom_data.label_bg')" required />
                    </div>
                    <div class="flex justify-end gap-3 px-6 pb-6">
                        <x-ui.button variant="secondary" type="button" @click="open = false">
                            {{ __('app.cancel') }}
                        </x-ui.button>
                        <x-ui.button variant="primary" type="submit">
                            {{ __('app.save') }}
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <x-ui.alert variant="success" :message="session('success')" class="mb-6" />
@endif
@if($errors->any())
    <x-ui.alert variant="error" :message="$errors->first()" class="mb-6" />
@endif

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
    class="flex flex-col gap-6">

    @forelse($categories as $catIdx => $category)
    <div x-show="catIds.includes({{ $category->id }})"
         :style="'order:' + catIds.indexOf({{ $category->id }})"
         style="display:flex;flex-direction:column"
         x-init="initFields({{ $category->id }}, {{ json_encode($category->fields->pluck('id')->toArray()) }})"
         class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">

        {{-- Category header --}}
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                {{-- Up/down --}}
                <div class="flex flex-col gap-0.5">
                    <button type="button"
                        @click="moveCatUp(catIds.indexOf({{ $category->id }}))"
                        :disabled="catIds.indexOf({{ $category->id }}) === 0"
                        class="p-0.5 rounded text-gray-400 hover:text-gray-600 disabled:opacity-20 disabled:cursor-not-allowed dark:hover:text-gray-200 transition-colors">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 15l-6-6-6 6"/></svg>
                    </button>
                    <button type="button"
                        @click="moveCatDown(catIds.indexOf({{ $category->id }}))"
                        :disabled="catIds.indexOf({{ $category->id }}) >= catIds.length - 1"
                        class="p-0.5 rounded text-gray-400 hover:text-gray-600 disabled:opacity-20 disabled:cursor-not-allowed dark:hover:text-gray-200 transition-colors">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                </div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $category->label() }}</h3>
                @if($category->is_system)
                <x-ui.badge variant="accent">{{ __('custom_data.system') }}</x-ui.badge>
                @endif
                <span class="text-sm text-gray-400 dark:text-gray-500">{{ $category->name_en }} / {{ $category->name_bg }}</span>
            </div>

            <div class="flex items-center gap-2">
                {{-- Add Field button + modal --}}
                <div x-data="{ open: false }">
                    <x-ui.button variant="secondary" size="sm" type="button" @click="open = true">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        {{ __('custom_data.add_field') }}
                    </x-ui.button>

                    <div x-show="open" x-cloak @keydown.escape.window="open = false"
                        class="fixed inset-0 z-99999 flex items-center justify-center p-5">
                        <div @click="open = false" class="absolute inset-0 bg-gray-400/50 backdrop-blur-[32px]"></div>
                        <div @click.stop class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-gray-900 shadow-xl">
                            <button @click="open = false"
                                class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            </button>
                            <div class="px-6 pt-6 pb-2 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('custom_data.add_field') }}</h3>
                            </div>
                            <form method="POST" action="{{ route('admin.custom-data.fields.store') }}"
                                x-data="{ fieldType: 'text' }">
                                @csrf
                                <input type="hidden" name="category_id" value="{{ $category->id }}">
                                <div class="p-6 flex flex-col gap-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <x-ui.input name="label_en" :label="__('custom_data.label_en')" required />
                                        <x-ui.input name="label_bg" :label="__('custom_data.label_bg')" required />
                                    </div>
                                    <x-ui.select
                                        name="type"
                                        :label="__('custom_data.field_type')"
                                        :options="['text' => 'text', 'textarea' => 'textarea', 'select' => 'select', 'checkbox' => 'checkbox']"
                                        x-model="fieldType"
                                    />
                                    <div x-show="fieldType === 'select' || fieldType === 'checkbox'" x-cloak>
                                        <div class="grid grid-cols-2 gap-3">
                                            <x-ui.textarea name="options_en" :label="__('custom_data.options_en')" :rows="4" placeholder="Male&#10;Female" />
                                            <x-ui.textarea name="options_bg" :label="__('custom_data.options_bg')" :rows="4" placeholder="Мъж&#10;Жена" />
                                        </div>
                                        <p class="mt-1 text-xs text-gray-400">{{ __('custom_data.options_hint') }}</p>
                                    </div>
                                    <div class="flex items-center gap-6">
                                        <x-ui.checkbox name="is_required" :label="__('custom_data.required')" value="1" />
                                        <x-ui.checkbox name="is_visible" :label="__('custom_data.visible')" value="1" :checked="true" />
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 px-6 pb-6">
                                    <x-ui.button variant="secondary" type="button" @click="open = false">
                                        {{ __('app.cancel') }}
                                    </x-ui.button>
                                    <x-ui.button variant="primary" type="submit">
                                        {{ __('app.save') }}
                                    </x-ui.button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @if(!$category->is_system)
                {{-- Edit Category --}}
                <div x-data="{ open: false }">
                    <x-ui.button variant="secondary" size="sm" type="button" @click="open = true">
                        {{ __('app.edit') }}
                    </x-ui.button>

                    <div x-show="open" x-cloak @keydown.escape.window="open = false"
                        class="fixed inset-0 z-99999 flex items-center justify-center p-5">
                        <div @click="open = false" class="absolute inset-0 bg-gray-400/50 backdrop-blur-[32px]"></div>
                        <div @click.stop class="relative w-full max-w-md rounded-3xl bg-white dark:bg-gray-900 shadow-xl">
                            <button @click="open = false"
                                class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            </button>
                            <div class="px-6 pt-6 pb-2 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('custom_data.edit_category') }}</h3>
                            </div>
                            <form method="POST" action="{{ route('admin.custom-data.categories.update', $category) }}">
                                @csrf @method('PUT')
                                <div class="p-6 flex flex-col gap-4">
                                    <x-ui.input name="name_en" :label="__('custom_data.label_en')" value="{{ $category->name_en }}" required />
                                    <x-ui.input name="name_bg" :label="__('custom_data.label_bg')" value="{{ $category->name_bg }}" required />
                                </div>
                                <div class="flex justify-end gap-3 px-6 pb-6">
                                    <x-ui.button variant="secondary" type="button" @click="open = false">
                                        {{ __('app.cancel') }}
                                    </x-ui.button>
                                    <x-ui.button variant="primary" type="submit">
                                        {{ __('app.save') }}
                                    </x-ui.button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Delete Category --}}
                <x-ui.button variant="danger" size="sm" type="button"
                    x-data
                    @click="$dispatch('confirm-action', { title: '{{ __('custom_data.delete_category') }}', message: '{{ __('custom_data.delete_category_confirm') }}', btnLabel: '{{ __('app.delete') }}', btnColor: '#dc2626', url: '{{ route('admin.custom-data.categories.destroy', $category) }}', method: 'DELETE', successAction: 'redirect', targetId: '{{ route('admin.custom-data.index') }}', toastMessage: '{{ addslashes(__('flash.custom_category_deleted')) }}', toastVariant: 'success' })">
                    {{ __('app.delete') }}
                </x-ui.button>
                @endif
            </div>
        </div>

        {{-- Fields table --}}
        @if($category->fields->isEmpty())
        <div class="px-6 py-8 text-center text-sm text-gray-400">{{ __('custom_data.no_fields') }}</div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800" style="display:table">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        <th class="px-4 py-3 w-8"></th>
                        <th class="px-6 py-3">{{ __('custom_data.field_label') }}</th>
                        <th class="px-6 py-3">{{ __('custom_data.field_type') }}</th>
                        <th class="px-6 py-3">{{ __('custom_data.field_required') }}</th>
                        <th class="px-6 py-3">{{ __('custom_data.field_visible') }}</th>
                        <th class="px-6 py-3">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($category->fields as $fIdx => $field)
                    <tr x-show="fieldIds[{{ $category->id }}] && fieldIds[{{ $category->id }}].includes({{ $field->id }})"
                        :style="'order:' + (fieldIds[{{ $category->id }}] ? fieldIds[{{ $category->id }}].indexOf({{ $field->id }}) : {{ $fIdx }})"
                        class="text-sm text-gray-700 dark:text-gray-300">
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-0.5">
                                <button type="button"
                                    @click="moveFieldUp({{ $category->id }}, fieldIds[{{ $category->id }}].indexOf({{ $field->id }}))"
                                    :disabled="fieldIds[{{ $category->id }}].indexOf({{ $field->id }}) === 0"
                                    class="p-0.5 rounded text-gray-400 hover:text-gray-600 disabled:opacity-20 disabled:cursor-not-allowed dark:hover:text-gray-200 transition-colors">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 15l-6-6-6 6"/></svg>
                                </button>
                                <button type="button"
                                    @click="moveFieldDown({{ $category->id }}, fieldIds[{{ $category->id }}].indexOf({{ $field->id }}))"
                                    :disabled="fieldIds[{{ $category->id }}].indexOf({{ $field->id }}) >= fieldIds[{{ $category->id }}].length - 1"
                                    class="p-0.5 rounded text-gray-400 hover:text-gray-600 disabled:opacity-20 disabled:cursor-not-allowed dark:hover:text-gray-200 transition-colors">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <div class="font-medium flex items-center gap-1.5">
                                {{ $field->label() }}
                                @if($field->is_system)
                                <x-ui.badge>{{ __('custom_data.system') }}</x-ui.badge>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400">{{ $field->label_en }} / {{ $field->label_bg }}</div>
                        </td>
                        <td class="px-6 py-3">
                            <x-ui.badge>{{ $field->type }}</x-ui.badge>
                        </td>
                        <td class="px-6 py-3">
                            @if($field->is_required)
                            <x-ui.badge variant="success">{{ __('app.yes') }}</x-ui.badge>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @if($field->is_visible)
                            <x-ui.badge variant="success">{{ __('app.yes') }}</x-ui.badge>
                            @else
                            <x-ui.badge variant="danger">{{ __('app.no') }}</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                {{-- Edit field modal --}}
                                <div x-data="{ open: false }">
                                    <x-ui.button variant="ghost" size="sm" type="button" @click="open = true">
                                        {{ __('app.edit') }}
                                    </x-ui.button>

                                    <div x-show="open" x-cloak @keydown.escape.window="open = false"
                                        class="fixed inset-0 z-99999 flex items-center justify-center p-5">
                                        <div @click="open = false" class="absolute inset-0 bg-gray-400/50 backdrop-blur-[32px]"></div>
                                        <div @click.stop class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-gray-900 shadow-xl">
                                            <button @click="open = false"
                                                class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                            </button>
                                            <div class="px-6 pt-6 pb-2 border-b border-gray-100 dark:border-gray-800">
                                                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('custom_data.edit_field') }}</h3>
                                            </div>
                                            <form method="POST" action="{{ route('admin.custom-data.fields.update', $field) }}">
                                                @csrf @method('PATCH')
                                                <div class="p-6 flex flex-col gap-4">
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <x-ui.input name="label_en" :label="__('custom_data.label_en')" value="{{ $field->label_en }}" required />
                                                        <x-ui.input name="label_bg" :label="__('custom_data.label_bg')" value="{{ $field->label_bg }}" required />
                                                    </div>
                                                    @if(in_array($field->type, ['select', 'checkbox']))
                                                    @php
                                                        $optEn = implode("\n", array_map(fn($o) => is_array($o) ? ($o['en'] ?? '') : $o, $field->options ?? []));
                                                        $optBg = implode("\n", array_map(fn($o) => is_array($o) ? ($o['bg'] ?? '') : '', $field->options ?? []));
                                                    @endphp
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <x-ui.textarea name="options_en" :label="__('custom_data.options_en')" :rows="4">{{ $optEn }}</x-ui.textarea>
                                                        <x-ui.textarea name="options_bg" :label="__('custom_data.options_bg')" :rows="4">{{ $optBg }}</x-ui.textarea>
                                                    </div>
                                                    <p class="mt-1 text-xs text-gray-400">{{ __('custom_data.options_hint') }}</p>
                                                    @endif
                                                    <div class="flex items-center gap-6">
                                                        <x-ui.checkbox name="is_required" :label="__('custom_data.required')" value="1" :checked="$field->is_required" />
                                                        <x-ui.checkbox name="is_visible" :label="__('custom_data.visible')" value="1" :checked="$field->is_visible" />
                                                    </div>
                                                </div>
                                                <div class="flex justify-end gap-3 px-6 pb-6">
                                                    <x-ui.button variant="secondary" type="button" @click="open = false">
                                                        {{ __('app.cancel') }}
                                                    </x-ui.button>
                                                    <x-ui.button variant="primary" type="submit">
                                                        {{ __('app.save') }}
                                                    </x-ui.button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                @if(!$field->is_system)
                                <x-ui.button variant="danger" size="sm" type="button"
                                    x-data
                                    @click="$dispatch('confirm-action', { title: '{{ __('custom_data.delete_field') }}', message: '{{ __('custom_data.delete_field_confirm') }}', btnLabel: '{{ __('app.delete') }}', btnColor: '#dc2626', url: '{{ route('admin.custom-data.fields.destroy', $field) }}', method: 'DELETE', successAction: 'redirect', targetId: '{{ route('admin.custom-data.index') }}', toastMessage: '{{ addslashes(__('flash.custom_field_deleted')) }}', toastVariant: 'success' })">
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
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 px-6 py-12 text-center">
        <p class="text-gray-500 dark:text-gray-400">{{ __('custom_data.no_categories') }}</p>
    </div>
    @endforelse

</div>

</x-layouts.admin>
