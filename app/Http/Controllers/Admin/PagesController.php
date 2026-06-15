<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PagesController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('sort_order')->orderBy('title')->paginate(15)->withQueryString();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:191',
            'slug'             => 'required|string|max:191|unique:pages,slug',
            'content'          => 'nullable|string',
            'excerpt'          => 'nullable|string|max:500',
            'is_published'     => 'boolean',
            'show_in_nav'      => 'boolean',
            'sort_order'       => 'nullable|integer',
            'meta_title'       => 'nullable|string|max:191',
            'meta_description' => 'nullable|string|max:500',
            'hero_image'       => 'nullable|url|max:500',
            'hero_title'       => 'nullable|string|max:191',
            'hero_subtitle'    => 'nullable|string|max:500',
            'hero_cta_label'   => 'nullable|string|max:191',
            'hero_cta_url'     => 'nullable|string|max:500',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['show_in_nav']  = $request->boolean('show_in_nav');

        Page::create($data);

        return redirect()->route('admin.pages.index')->with('success', __('flash.page_created'));
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:191',
            'slug'             => ['required', 'string', 'max:191', Rule::unique('pages', 'slug')->ignore($page->id)],
            'content'          => 'nullable|string',
            'excerpt'          => 'nullable|string|max:500',
            'is_published'     => 'boolean',
            'show_in_nav'      => 'boolean',
            'sort_order'       => 'nullable|integer',
            'meta_title'       => 'nullable|string|max:191',
            'meta_description' => 'nullable|string|max:500',
            'hero_image'       => 'nullable|url|max:500',
            'hero_title'       => 'nullable|string|max:191',
            'hero_subtitle'    => 'nullable|string|max:500',
            'hero_cta_label'   => 'nullable|string|max:191',
            'hero_cta_url'     => 'nullable|string|max:500',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['show_in_nav']  = $request->boolean('show_in_nav');

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', __('flash.page_updated'));
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', __('flash.page_deleted'));
    }
}