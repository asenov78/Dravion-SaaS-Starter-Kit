<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PagesController extends Controller
{
    public function __construct(private HtmlSanitizer $sanitizer) {}

    private function languages(): \Illuminate\Database\Eloquent\Collection
    {
        return Language::orderByDesc('is_default')->orderBy('name')->get();
    }

    public function index()
    {
        $pages = Page::orderBy('sort_order')->orderBy('title')->paginate(15)->withQueryString();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $languages = $this->languages();
        return view('admin.pages.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug'             => 'required|string|max:191|unique:pages,slug',
            'is_published'     => 'boolean',
            'show_in_nav'      => 'boolean',
            'sort_order'       => 'nullable|integer',
            'hero_image'       => 'nullable|url|max:500',
            'hero_cta_url'     => 'nullable|url|max:500',
            'translations'            => 'array',
            'translations.*.title'    => 'nullable|string|max:191',
            'translations.*.content'  => 'nullable|string',
            'translations.*.excerpt'  => 'nullable|string|max:1000',
            'translations.*.hero_title'     => 'nullable|string|max:191',
            'translations.*.hero_subtitle'  => 'nullable|string|max:500',
            'translations.*.hero_cta_label' => 'nullable|string|max:191',
            'translations.*.meta_title'     => 'nullable|string|max:191',
            'translations.*.meta_description' => 'nullable|string|max:500',
        ]);

        // Derive primary fields from default language translation
        $langs   = $this->languages();
        $default = $langs->firstWhere('is_default', true) ?? $langs->first();
        $defTrans = $request->input('translations.' . ($default?->code ?? 'en'), []);

        $page = Page::create([
            'slug'         => $data['slug'],
            'title'        => $defTrans['title'] ?? '',
            'content'      => $this->sanitizer->sanitize($defTrans['content'] ?? null),
            'excerpt'      => $defTrans['excerpt'] ?? null,
            'hero_title'   => $defTrans['hero_title'] ?? null,
            'hero_subtitle'=> $defTrans['hero_subtitle'] ?? null,
            'hero_cta_label' => $defTrans['hero_cta_label'] ?? null,
            'meta_title'   => $defTrans['meta_title'] ?? null,
            'meta_description' => $defTrans['meta_description'] ?? null,
            'is_published' => $request->boolean('is_published'),
            'show_in_nav'  => $request->boolean('show_in_nav'),
            'sort_order'   => $data['sort_order'] ?? 0,
            'hero_image'   => $data['hero_image'] ?? null,
            'hero_cta_url' => $data['hero_cta_url'] ?? null,
        ]);

        $this->saveTranslations($page, $request->input('translations', []));

        return redirect()->route('admin.pages.index')->with('success', __('flash.page_created'));
    }

    public function edit(Page $page)
    {
        $languages = $this->languages();
        $page->load('translations');
        return view('admin.pages.edit', compact('page', 'languages'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'slug'             => ['required', 'string', 'max:191', Rule::unique('pages', 'slug')->ignore($page->id)],
            'is_published'     => 'boolean',
            'show_in_nav'      => 'boolean',
            'sort_order'       => 'nullable|integer',
            'hero_image'       => 'nullable|url|max:500',
            'hero_cta_url'     => 'nullable|url|max:500',
            'translations'            => 'array',
            'translations.*.title'    => 'nullable|string|max:191',
            'translations.*.content'  => 'nullable|string',
            'translations.*.excerpt'  => 'nullable|string|max:1000',
            'translations.*.hero_title'     => 'nullable|string|max:191',
            'translations.*.hero_subtitle'  => 'nullable|string|max:500',
            'translations.*.hero_cta_label' => 'nullable|string|max:191',
            'translations.*.meta_title'     => 'nullable|string|max:191',
            'translations.*.meta_description' => 'nullable|string|max:500',
        ]);

        // Sync primary fields from default language
        $langs   = $this->languages();
        $default = $langs->firstWhere('is_default', true) ?? $langs->first();
        $defTrans = $request->input('translations.' . ($default?->code ?? 'en'), []);

        $page->update([
            'slug'         => $data['slug'],
            'title'        => $defTrans['title'] ?? $page->title,
            'content'      => $this->sanitizer->sanitize($defTrans['content'] ?? null) ?? $page->content,
            'excerpt'      => $defTrans['excerpt'] ?? $page->excerpt,
            'hero_title'   => $defTrans['hero_title'] ?? $page->hero_title,
            'hero_subtitle'=> $defTrans['hero_subtitle'] ?? $page->hero_subtitle,
            'hero_cta_label' => $defTrans['hero_cta_label'] ?? $page->hero_cta_label,
            'meta_title'   => $defTrans['meta_title'] ?? $page->meta_title,
            'meta_description' => $defTrans['meta_description'] ?? $page->meta_description,
            'is_published' => $request->boolean('is_published'),
            'show_in_nav'  => $request->boolean('show_in_nav'),
            'sort_order'   => $data['sort_order'] ?? $page->sort_order,
            'hero_image'   => $data['hero_image'] ?? $page->hero_image,
            'hero_cta_url' => $data['hero_cta_url'] ?? $page->hero_cta_url,
        ]);

        $this->saveTranslations($page, $request->input('translations', []));

        return redirect()->route('admin.pages.index')->with('success', __('flash.page_updated'));
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', __('flash.page_deleted'));
    }

    private function saveTranslations(Page $page, array $translations): void
    {
        foreach ($translations as $locale => $fields) {
            PageTranslation::updateOrCreate(
                ['page_id' => $page->id, 'locale' => $locale],
                array_filter([
                    'title'            => $fields['title'] ?? null,
                    'content'          => $this->sanitizer->sanitize($fields['content'] ?? null),
                    'excerpt'          => $fields['excerpt'] ?? null,
                    'hero_title'       => $fields['hero_title'] ?? null,
                    'hero_subtitle'    => $fields['hero_subtitle'] ?? null,
                    'hero_cta_label'   => $fields['hero_cta_label'] ?? null,
                    'meta_title'       => $fields['meta_title'] ?? null,
                    'meta_description' => $fields['meta_description'] ?? null,
                ], fn($v) => $v !== null)
            );
        }
    }
}