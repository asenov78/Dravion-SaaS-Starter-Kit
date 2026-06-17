<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\LanguageLine;
use App\Services\LangKeyExtractor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::orderByDesc('is_default')->orderBy('name')->get();
        return view('admin.languages.index', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:languages,code',
            'name' => 'required|string|max:100',
            'flag' => 'nullable|string|max:10',
        ]);

        $language = Language::create([
            'code'       => strtolower($request->code),
            'name'       => $request->name,
            'flag'       => $request->flag ?? '',
            'is_default' => Language::count() === 0,
        ]);

        // Seed from lang/en source files — these are the canonical key list
        $keys = LangKeyExtractor::keys('en');

        // Fall back to keys already in DB if no lang files exist
        if (empty($keys)) {
            $keys = LanguageLine::whereNot('language_id', $language->id)
                ->distinct()->pluck('key')->toArray();
        }

        foreach ($keys as $key) {
            $language->lines()->firstOrCreate(['key' => $key], ['value' => '']);
        }

        return redirect()->route('admin.languages.index')->with('success', __('flash.language_added'));
    }

    public function destroy(Language $language)
    {
        $remaining = Language::where('id', '!=', $language->id)->count();
        abort_if($remaining === 0, 403, 'Cannot delete the last language.');

        if ($language->is_default) {
            Language::where('id', '!=', $language->id)->oldest()->first()
                ?->update(['is_default' => true]);
        }

        $language->delete();

        return redirect()->route('admin.languages.index')->with('success', __('flash.language_deleted'));
    }

    public function edit(Language $language)
    {
        $search = request('search', '');
        $lines  = $language->lines()
            ->when($search, fn ($q) => $q->where('key', 'like', "%{$search}%")
                ->orWhere('value', 'like', "%{$search}%"))
            ->orderBy('key')
            ->paginate(50)
            ->withQueryString();

        $enValues = LangKeyExtractor::keyValues('en');

        return view('admin.languages.edit', compact('language', 'lines', 'enValues', 'search'));
    }

    public function batch(Request $request, Language $language)
    {
        $data = $request->input('lines', []);

        foreach ($data as $key => $value) {
            $language->lines()->updateOrCreate(
                ['key'   => $key],
                ['value' => (string) $value],
            );
        }

        \App\Translation\DatabaseLoader::clearCache($language->code);

        return redirect()->back()->with('success', __('flash.translations_saved', ['count' => count($data)]));
    }

    public function reseed(Language $language)
    {
        $keyValues = LangKeyExtractor::keyValues('en');

        $nativeValues = LangKeyExtractor::keyValues($language->code);

        foreach ($keyValues as $key => $enValue) {
            if (isset($nativeValues[$key])) {
                // Native lang file exists — use it (overwrite)
                $language->lines()->updateOrCreate(['key' => $key], ['value' => $nativeValues[$key]]);
            } else {
                // No native file — keep existing or create empty
                $language->lines()->firstOrCreate(['key' => $key], ['value' => '']);
            }
        }

        return redirect()->route('admin.languages.index')
            ->with('success', __('flash.keys_synced', ['count' => count($keyValues), 'name' => $language->name]));
    }

    public function setDefault(Language $language)
    {
        Language::query()->update(['is_default' => false]);
        $language->update(['is_default' => true]);

        return redirect()->route('admin.languages.index')->with('success', __('flash.default_set', ['name' => $language->name]));
    }

    public function updateLine(Request $request, Language $language)
    {
        $request->validate([
            'key'   => 'required|string',
            'value' => 'required|string',
        ]);

        LanguageLine::updateOrCreate(
            ['language_id' => $language->id, 'key' => $request->key],
            ['value' => $request->value],
        );

        // Seed this key into all other languages that don't have it yet
        Language::where('id', '!=', $language->id)->each(function ($other) use ($request) {
            $other->lines()->firstOrCreate(['key' => $request->key], ['value' => '']);
        });

        return redirect()->back()->with('success', __('flash.translation_saved'));
    }

    public function meta(Language $language)
    {
        return view('admin.languages.meta', compact('language'));
    }

    public function updateMeta(Request $request, Language $language)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => "nullable|string|max:10|unique:languages,code,{$language->id}",
            'flag' => 'nullable|string|max:10',
        ]);

        $language->update([
            'name' => $request->name,
            'flag' => $request->flag ?? $language->flag,
        ]);

        return redirect()->route('admin.languages.index')->with('success', __('flash.language_updated'));
    }

    public function export(Language $language)
    {
        $lines = $language->lines()->pluck('value', 'key')->toArray();

        return response()->json($lines)
            ->header('Content-Disposition', "attachment; filename=\"{$language->code}.json\"");
    }

    public function import(Request $request, Language $language)
    {
        $request->validate(['json' => 'required|string']);

        $data = json_decode($request->json, true);

        if (! is_array($data)) {
            return redirect()->back()->withErrors(['json' => 'Invalid JSON.']);
        }

        foreach ($data as $key => $value) {
            LanguageLine::updateOrCreate(
                ['language_id' => $language->id, 'key' => (string) $key],
                ['value' => (string) $value],
            );
        }

        return redirect()->route('admin.languages.index')
            ->with('success', __('flash.lines_imported', ['count' => count($data), 'name' => $language->name]));
    }
}
