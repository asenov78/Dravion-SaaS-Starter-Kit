<x-layouts.admin :title="__('nav.custom_data')">

@php
$reorderCategoriesUrl = route('admin.custom-data.categories.reorder');
$reorderFieldsUrl     = route('admin.custom-data.fields.reorder');
$csrfToken            = csrf_token();

$iconPlus   = new \Illuminate\Support\HtmlString('<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>');
$iconEdit   = new \Illuminate\Support\HtmlString('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>');
$iconTrash  = new \Illuminate\Support\HtmlString('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>');
$iconSave   = new \Illuminate\Support\HtmlString('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>');
$iconCancel = new \Illuminate\Support\HtmlString('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>');
@endphp

{{-- ── Page header ──────────────────────────────────────────────────────── --}}
<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('nav.custom_data') }}</h2>

    {{-- Add Category — inline modal --}}
    <div x-data="{ open: false }">
        <x-ta.button type="button" variant="primary" size="sm" :startIcon="$iconPlus" @click="open = true">
            {{ __('custom_data.add_category') }}
        </x-ta.button>

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
                        <x-ta.button type="button" variant="outline" size="sm" :startIcon="$iconCancel" @click="open = false">
                            {{ __('app.cancel') }}
                        </x-ta.button>
                        <x-ta.button type="submit" variant="primary" size="sm" :startIcon="$iconSave">
                            {{ __('app.save') }}
                        </x-ta.button>
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
                {{-- Up/down arrows --}}
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
                <span class="inline-flex items-center rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ __('custom_data.system') }}</span>
                @endif
                <span class="text-sm text-gray-400 dark:text-gray-500">{{ $category->name_en }} / {{ $category->name_bg }}</span>
            </div>

            <div class="flex items-center gap-2">
                {{-- Add Field button + modal --}}
                <div x-data="{ open: false }">
                    <x-ta.button type="button" variant="outline" size="sm" :startIcon="$iconPlus" @click="open = true">
                        {{ __('custom_data.add_field') }}
                    </x-ta.button>

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
                                        <select name="type" x-model="fieldType"
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                            <option value="text">text</option>
                                            <option value="textarea">textarea</option>
                                            <option value="select">select</option>
                                            <option value="checkbox">checkbox</option>
                                        </select>
                                    </div>
                                    <div x-show="fieldType === 'select' || fieldType === 'checkbox'" x-cloak>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.options_en') }}</label>
                                                <textarea name="options_en" rows="4"
                                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                                    placeholder="Male&#10;Female"></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.options_bg') }}</label>
                                                <textarea name="options_bg" rows="4"
                                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                                    placeholder="Мъж&#10;Жена"></textarea>
                                            </div>
                                        </div>
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
                                    <x-ta.button type="button" variant="outline" size="sm" :startIcon="$iconCancel" @click="open = false">
                                        {{ __('app.cancel') }}
                                    </x-ta.button>
                                    <x-ta.button type="submit" variant="primary" size="sm" :startIcon="$iconSave">
                                        {{ __('app.save') }}
                                    </x-ta.button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @if(!$category->is_system)
                {{-- Edit Category --}}
                <div x-data="{ open: false }">
                    <x-ta.button type="button" variant="outline" size="sm" :startIcon="$iconEdit" @click="open = true">
                        {{ __('app.edit') }}
                    </x-ta.button>

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
                                    <x-ta.button type="button" variant="outline" size="sm" :startIcon="$iconCancel" @click="open = false">
                                        {{ __('app.cancel') }}
                                    </x-ta.button>
                                    <x-ta.button type="submit" variant="primary" size="sm" :startIcon="$iconSave">
                                        {{ __('app.save') }}
                                    </x-ta.button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Delete Category --}}
                <x-ta.button type="button" variant="outline" size="sm" :startIcon="$iconTrash"
                    x-data
                    @click="$dispatch('confirm-action', { title: '{{ __('custom_data.delete_category') }}', message: '{{ __('custom_data.delete_category_confirm') }}', btnLabel: '{{ __('app.delete') }}', btnColor: '#dc2626', url: '{{ route('admin.custom-data.categories.destroy', $category) }}', method: 'DELETE', successAction: 'redirect', targetId: '{{ route('admin.custom-data.index') }}', toastMessage: '{{ addslashes(__('flash.custom_category_deleted')) }}', toastVariant: 'success' })">
                    {{ __('app.delete') }}
                </x-ta.button>
                @endif
            </div>
        </div>

        {{-- Fields list (flex — supports CSS order for reorder) --}}
        @if($category->fields->isEmpty())
        <div class="px-6 py-8 text-center text-sm text-gray-400">{{ __('custom_data.no_fields') }}</div>
        @else
        {{-- Header row --}}
        <div class="flex items-center px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-100 dark:border-gray-800">
            <div style="width:40px"></div>
            <div class="flex-1 px-2">{{ __('custom_data.field_label') }}</div>
            <div style="width:110px" class="px-2">{{ __('custom_data.field_type') }}</div>
            <div style="width:90px" class="px-2">{{ __('custom_data.field_required') }}</div>
            <div style="width:90px" class="px-2">{{ __('custom_data.field_visible') }}</div>
            <div style="width:200px" class="px-2">{{ __('app.actions') }}</div>
        </div>
        {{-- Field rows — flex container so CSS order works --}}
        <div style="display:flex;flex-direction:column">
            @foreach($category->fields as $fIdx => $field)
            <div
                x-show="fieldIds[{{ $category->id }}] && fieldIds[{{ $category->id }}].includes({{ $field->id }})"
                :style="'order:' + (fieldIds[{{ $category->id }}] ? fieldIds[{{ $category->id }}].indexOf({{ $field->id }}) : {{ $fIdx }})"
                class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-gray-800 last:border-0">

                {{-- Reorder arrows --}}
                <div style="width:40px" class="flex flex-col gap-0.5">
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

                {{-- Label --}}
                <div class="flex-1 px-2">
                    <div class="font-medium flex items-center gap-1.5">
                        {{ $field->label() }}
                        @if($field->is_system)
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500 dark:bg-gray-800">{{ __('custom_data.system') }}</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-400">{{ $field->label_en }} / {{ $field->label_bg }}</div>
                </div>

                {{-- Type --}}
                <div style="width:110px" class="px-2">
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $field->type }}</span>
                </div>

                {{-- Required --}}
                <div style="width:90px" class="px-2">
                    @if($field->is_required)
                    <span class="inline-flex items-center rounded-full bg-success-50 px-2 py-0.5 text-xs text-success-600 dark:bg-success-500/10 dark:text-success-400">{{ __('app.yes') }}</span>
                    @else
                    <span class="text-gray-400">—</span>
                    @endif
                </div>

                {{-- Visible --}}
                <div style="width:90px" class="px-2">
                    @if($field->is_visible)
                    <span class="inline-flex items-center rounded-full bg-success-50 px-2 py-0.5 text-xs text-success-600 dark:bg-success-500/10 dark:text-success-400">{{ __('app.yes') }}</span>
                    @else
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500 dark:bg-gray-800">{{ __('app.no') }}</span>
                    @endif
                </div>

                {{-- Actions --}}
                <div style="width:200px" class="px-2 flex items-center gap-2">
                    {{-- Edit field modal --}}
                    <div x-data="{ open: false }">
                        <x-ta.button type="button" variant="outline" size="sm" :startIcon="$iconEdit" @click="open = true">
                            {{ __('app.edit') }}
                        </x-ta.button>

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
                                        @if(in_array($field->type, ['select', 'checkbox']))
                                        @php
                                            $optEn = implode("\n", array_map(fn($o) => is_array($o) ? ($o['en'] ?? '') : $o, $field->options ?? []));
                                            $optBg = implode("\n", array_map(fn($o) => is_array($o) ? ($o['bg'] ?? '') : '', $field->options ?? []));
                                        @endphp
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.options_en') }}</label>
                                                <textarea name="options_en" rows="4"
                                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ $optEn }}</textarea>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ __('custom_data.options_bg') }}</label>
                                                <textarea name="options_bg" rows="4"
                                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ $optBg }}</textarea>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-400">{{ __('custom_data.options_hint') }}</p>
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
                                        <x-ta.button type="button" variant="outline" size="sm" :startIcon="$iconCancel" @click="open = false">
                                            {{ __('app.cancel') }}
                                        </x-ta.button>
                                        <x-ta.button type="submit" variant="primary" size="sm" :startIcon="$iconSave">
                                            {{ __('app.save') }}
                                        </x-ta.button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @if(!$field->is_system)
                    <x-ta.button type="button" variant="outline" size="sm" :startIcon="$iconTrash"
                        x-data
                        @click="$dispatch('confirm-action', { title: '{{ __('custom_data.delete_field') }}', message: '{{ __('custom_data.delete_field_confirm') }}', btnLabel: '{{ __('app.delete') }}', btnColor: '#dc2626', url: '{{ route('admin.custom-data.fields.destroy', $field) }}', method: 'DELETE', successAction: 'redirect', targetId: '{{ route('admin.custom-data.index') }}', toastMessage: '{{ addslashes(__('flash.custom_field_deleted')) }}', toastVariant: 'success' })">
                        {{ __('app.delete') }}
                    </x-ta.button>
                    @endif
                </div>
            </div>
            @endforeach
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
