<x-layouts.admin :title="__('pages.create')">

<style>
.tiptap-toolbar { display:flex; flex-wrap:wrap; gap:2px; padding:6px 8px; border-bottom:1px solid #e5e7eb; background:#f9fafb; }
.dark .tiptap-toolbar { background:#111827; border-color:#1f2937; }
.tiptap-btn { padding:4px 8px; border-radius:5px; font-size:12px; font-weight:600; color:#374151; cursor:pointer; transition:background .15s; border:none; background:transparent; }
.tiptap-btn:hover { background:#e5e7eb; }
.dark .tiptap-btn { color:#d1d5db; }
.dark .tiptap-btn:hover { background:#1f2937; }
.tiptap-btn.active { background:#6366f1; color:#fff; }
.tiptap-sep { width:1px; background:#e5e7eb; margin:2px 4px; }
.dark .tiptap-sep { background:#1f2937; }
.tiptap-editor .ProseMirror { min-height:220px; padding:14px 16px; outline:none; font-size:14px; line-height:1.7; color:#111827; }
.dark .tiptap-editor .ProseMirror { color:#f3f4f6; }
.tiptap-editor .ProseMirror p.is-editor-empty:first-child::before { content:attr(data-placeholder); color:#9ca3af; pointer-events:none; float:left; height:0; }
.tiptap-editor .ProseMirror h2 { font-size:1.4em; font-weight:700; margin:.6em 0 .3em; }
.tiptap-editor .ProseMirror h3 { font-size:1.15em; font-weight:700; margin:.5em 0 .25em; }
.tiptap-editor .ProseMirror ul { list-style:disc; padding-left:1.5em; }
.tiptap-editor .ProseMirror ol { list-style:decimal; padding-left:1.5em; }
.tiptap-editor .ProseMirror blockquote { border-left:3px solid #6366f1; padding-left:12px; color:#6b7280; margin:0; }
.tiptap-editor .ProseMirror code { background:#f3f4f6; border-radius:4px; padding:1px 4px; font-size:.875em; }
.dark .tiptap-editor .ProseMirror code { background:#1f2937; }
.tiptap-editor .ProseMirror pre { background:#1e1e2e; color:#cdd6f4; border-radius:8px; padding:12px 16px; overflow-x:auto; }
.tiptap-editor .ProseMirror a { color:#6366f1; text-decoration:underline; }
/* cms-content preview panel */
.cms-content h1,.cms-content h2,.cms-content h3,.cms-content h4 { font-weight:700; margin:.75em 0 .4em; line-height:1.3; color:#111827; }
.cms-content h2 { font-size:1.4em; }
.cms-content h3 { font-size:1.15em; }
.cms-content p { margin:.6em 0; color:#374151; line-height:1.7; }
.cms-content ul,.cms-content ol { padding-left:1.5em; margin:.5em 0; }
.cms-content ul { list-style:disc; }
.cms-content ol { list-style:decimal; }
.cms-content li { margin:.25em 0; color:#374151; }
.cms-content a { color:#6366f1; text-decoration:underline; }
.cms-content blockquote { border-left:3px solid #6366f1; padding-left:12px; color:#6b7280; margin:.75em 0; }
.cms-content code { background:#f3f4f6; border-radius:4px; padding:1px 4px; font-size:.875em; }
.cms-content pre { background:#1e1e2e; color:#cdd6f4; border-radius:8px; padding:12px 16px; overflow-x:auto; margin:.75em 0; }
.cms-content hr { border:none; border-top:1px solid #e5e7eb; margin:1.5em 0; }
.cms-content strong { font-weight:700; }
</style>

@php $defaultLang = $languages->firstWhere('is_default', true) ?? $languages->first(); @endphp

<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
            <a href="{{ route('admin.pages.index') }}" class="hover:text-brand-500">{{ __('pages.title') }}</a>
            <span>/</span>
            <span class="text-gray-800 dark:text-white/90">{{ __('pages.create') }}</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('pages.create') }}</h2>
    </div>
    <a href="{{ route('admin.pages.index') }}"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        {{ __('app.back') }}
    </a>
</div>

@if($errors->any())
    <x-ui.alert variant="error" :message="$errors->first()" class="mb-6" />
@endif

<form method="POST" action="{{ route('admin.pages.store') }}" class="flex flex-col gap-6"
      x-data="{ activeTab: '{{ $defaultLang?->code ?? 'en' }}' }">
    @csrf

    {{-- Language Tabs --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-0 px-6 pt-4 border-b border-gray-100 dark:border-gray-800 overflow-x-auto">
            @foreach($languages as $lang)
            <button type="button"
                @click="activeTab = '{{ $lang->code }}'"
                :class="activeTab === '{{ $lang->code }}'
                    ? 'border-b-2 border-brand-500 text-brand-600 dark:text-brand-400 font-semibold'
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                class="flex items-center gap-2 px-4 py-3 text-sm whitespace-nowrap transition-colors">
                <span>{{ $lang->flag }}</span> {{ $lang->name }}
                @if($lang->is_default) <span class="text-xs bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 px-1.5 rounded">{{ __('app.default') }}</span> @endif
            </button>
            @endforeach
        </div>

        @foreach($languages as $lang)
        <div x-show="activeTab === '{{ $lang->code }}'" x-cloak class="p-6 flex flex-col gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('app.name') }} @if($lang->is_default)<span class="text-error-500">*</span>@endif</label>
                <input type="text" name="translations[{{ $lang->code }}][title]"
                    value="{{ old('translations.'.$lang->code.'.title') }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('pages.excerpt') }}</label>
                <textarea name="translations[{{ $lang->code }}][excerpt]" rows="2"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">{{ old('translations.'.$lang->code.'.excerpt') }}</textarea>
            </div>

            {{-- TipTap editor --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('pages.content') }}</label>
                <div x-data="tiptap({ content: {{ json_encode(old('translations.'.$lang->code.'.content', '')) }}, placeholder: '{{ __('pages.content') }}...' })"
                     class="tiptap-editor rounded-lg border border-gray-300 dark:border-gray-700 overflow-hidden"
                     :style="showPreview ? 'display:flex;align-items:stretch;' : ''"
                     @destroy.window="destroy()">
                    {{-- Left: toolbar + editor --}}
                    <div :style="showPreview ? 'width:50%;min-width:0;flex-shrink:0;' : 'width:100%;'">
                        <div class="tiptap-toolbar">
                            <button type="button" @click="execCmd('bold')" :class="{active: isActive('bold')}" class="tiptap-btn" title="Bold"><b>B</b></button>
                            <button type="button" @click="execCmd('italic')" :class="{active: isActive('italic')}" class="tiptap-btn" title="Italic"><i>I</i></button>
                            <button type="button" @click="execCmd('strike')" :class="{active: isActive('strike')}" class="tiptap-btn" title="Strike"><s>S</s></button>
                            <div class="tiptap-sep"></div>
                            <button type="button" @click="execCmd('h2')" :class="{active: isActive('heading',{level:2})}" class="tiptap-btn">H2</button>
                            <button type="button" @click="execCmd('h3')" :class="{active: isActive('heading',{level:3})}" class="tiptap-btn">H3</button>
                            <div class="tiptap-sep"></div>
                            <button type="button" @click="execCmd('ul')" :class="{active: isActive('bulletList')}" class="tiptap-btn">&#8226; UL</button>
                            <button type="button" @click="execCmd('ol')" :class="{active: isActive('orderedList')}" class="tiptap-btn">1. OL</button>
                            <div class="tiptap-sep"></div>
                            <button type="button" @click="execCmd('blockquote')" :class="{active: isActive('blockquote')}" class="tiptap-btn">&ldquo;</button>
                            <button type="button" @click="execCmd('code')" :class="{active: isActive('code')}" class="tiptap-btn">`</button>
                            <button type="button" @click="execCmd('codeBlock')" :class="{active: isActive('codeBlock')}" class="tiptap-btn">&lt;/&gt;</button>
                            <div class="tiptap-sep"></div>
                            <button type="button" @click="execCmd('link')" :class="{active: isActive('link')}" class="tiptap-btn">&#128279;</button>
                            <button type="button" @click="execCmd('unlink')" class="tiptap-btn">&#10006;</button>
                            <button type="button" @click="execCmd('hr')" class="tiptap-btn">&#8212;</button>
                            <div class="tiptap-sep"></div>
                            <button type="button" @click="execCmd('undo')" class="tiptap-btn">&#8630;</button>
                            <button type="button" @click="execCmd('redo')" class="tiptap-btn">&#8631;</button>
                            <div class="tiptap-sep"></div>
                            <button type="button" @click="showPreview = !showPreview" :class="{active: showPreview}" class="tiptap-btn" style="margin-left:auto;">👁 {{ __('app.preview') }}</button>
                        </div>
                        <div x-ref="editorEl"></div>
                        <textarea data-tiptap-target name="translations[{{ $lang->code }}][content]"
                            class="hidden">{{ old('translations.'.$lang->code.'.content') }}</textarea>
                    </div>
                    {{-- Right: preview panel --}}
                    <div x-show="showPreview" class="cms-content"
                         style="width:50%;border-left:1px solid #e5e7eb;overflow-y:auto;min-height:500px;padding:20px 24px;background:#fff;"
                         x-html="content">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('pages.hero_title') }}</label>
                    <input type="text" name="translations[{{ $lang->code }}][hero_title]"
                        value="{{ old('translations.'.$lang->code.'.hero_title') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('pages.hero_cta_label') }}</label>
                    <input type="text" name="translations[{{ $lang->code }}][hero_cta_label]"
                        value="{{ old('translations.'.$lang->code.'.hero_cta_label') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('pages.hero_subtitle') }}</label>
                <textarea name="translations[{{ $lang->code }}][hero_subtitle]" rows="2"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">{{ old('translations.'.$lang->code.'.hero_subtitle') }}</textarea>
            </div>
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('pages.meta_title') }}</label>
                    <input type="text" name="translations[{{ $lang->code }}][meta_title]"
                        value="{{ old('translations.'.$lang->code.'.meta_title') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('pages.meta_desc') }}</label>
                    <textarea name="translations[{{ $lang->code }}][meta_description]" rows="2"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">{{ old('translations.'.$lang->code.'.meta_description') }}</textarea>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Common fields --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('app.settings') }}</h3>
        </div>
        <div class="p-6 flex flex-col gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('pages.slug') }} <span class="text-error-500">*</span></label>
                <input type="text" name="slug" value="{{ old('slug') }}" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800 {{ $errors->has('slug') ? 'border-error-400' : '' }}" />
                <p class="mt-1.5 text-xs text-gray-400">{{ __('pages.slug_hint') }}</p>
                @error('slug') <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800">
                    <label for="is_published" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('pages.published') }}</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="hidden" name="show_in_nav" value="0">
                    <input type="checkbox" name="show_in_nav" id="show_in_nav" value="1" {{ old('show_in_nav') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800">
                    <label for="show_in_nav" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('pages.in_nav') }}</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('pages.sort') }}</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('pages.hero_image') }}</label>
                <input type="url" name="hero_image" value="{{ old('hero_image') }}"
                    placeholder="https://images.unsplash.com/photo-..."
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('pages.hero_cta_url') }}</label>
                <input type="text" name="hero_cta_url" value="{{ old('hero_cta_url') }}"
                    placeholder="/register"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.pages.index') }}"
            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800">
            {{ __('app.cancel') }}
        </a>
        <button type="submit"
            class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            {{ __('pages.create') }}
        </button>
    </div>
</form>

</x-layouts.admin>