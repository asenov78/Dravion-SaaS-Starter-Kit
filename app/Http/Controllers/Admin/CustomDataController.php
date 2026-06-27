<?php

namespace App\Http\Controllers\Admin;

use App\Facades\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\CustomCategory;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomDataController extends Controller
{
    public function index()
    {
        $categories = CustomCategory::where('entity', 'users')
            ->with(['fields' => fn($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('admin.custom-data.index', compact('categories'));
    }

    // ── Reorder ──────────────────────────────────────────────────────────────

    public function reorderCategories(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        foreach ($request->ids as $position => $id) {
            CustomCategory::where('id', $id)->update(['sort_order' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }

    public function reorderFields(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        foreach ($request->ids as $position => $id) {
            CustomField::where('id', $id)->update(['sort_order' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }

    // ── Categories ───────────────────────────────────────────────────────────

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name_en' => 'required|string|max:191',
            'name_bg' => 'required|string|max:191',
        ]);

        $key = Str::snake(Str::lower($data['name_en']));
        $sort = (CustomCategory::where('entity', 'users')->max('sort_order') ?? 0) + 10;

        $category = CustomCategory::create([
            'entity'     => 'users',
            'key'        => $key . '_' . uniqid(),
            'name_en'    => $data['name_en'],
            'name_bg'    => $data['name_bg'],
            'is_system'  => false,
            'sort_order' => $sort,
        ]);

        ActivityLogger::log('custom_data', 'category_created',
            "Created category {$category->name_en}", $category);

        return redirect()->route('admin.custom-data.index')
            ->with('success', __('flash.custom_category_created'));
    }

    public function updateCategory(Request $request, CustomCategory $customCategory)
    {
        if ($customCategory->is_system) {
            abort(403, 'System categories cannot be updated.');
        }

        $data = $request->validate([
            'name_en' => 'required|string|max:191',
            'name_bg' => 'required|string|max:191',
        ]);

        $customCategory->update($data);

        ActivityLogger::log('custom_data', 'category_updated',
            "Updated category {$customCategory->name_en}", $customCategory);

        return redirect()->route('admin.custom-data.index')
            ->with('success', __('flash.custom_category_updated'));
    }

    public function destroyCategory(CustomCategory $customCategory)
    {
        if ($customCategory->is_system) {
            abort(403, 'System categories cannot be deleted.');
        }

        $name = $customCategory->name_en;
        $customCategory->delete();

        ActivityLogger::log('custom_data', 'category_deleted',
            "Deleted category {$name}");

        return redirect()->route('admin.custom-data.index')
            ->with('success', __('flash.custom_category_deleted'));
    }

    // ── Fields ───────────────────────────────────────────────────────────────

    public function storeField(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:custom_categories,id',
            'label_en'    => 'required|string|max:191',
            'label_bg'    => 'required|string|max:191',
            'type'        => 'required|in:text,textarea,select,checkbox',
            'options_en'  => 'nullable|string',
            'options_bg'  => 'nullable|string',
            'is_required' => 'boolean',
            'is_visible'  => 'boolean',
        ]);

        $cat  = CustomCategory::findOrFail($data['category_id']);

        if ($cat->key === 'account') {
            abort(403, 'Cannot add custom fields to the account category.');
        }

        $sort = ($cat->fields()->max('sort_order') ?? 0) + 10;
        $key  = Str::snake(Str::lower($data['label_en'])) . '_' . uniqid();

        $options = null;
        if (in_array($data['type'], ['select', 'checkbox']) && !empty($data['options_en'])) {
            $options = $this->parseMultilingualOptions($data['options_en'] ?? '', $data['options_bg'] ?? '');
        }

        $field = CustomField::create([
            'category_id' => $data['category_id'],
            'key'         => $key,
            'label_en'    => $data['label_en'],
            'label_bg'    => $data['label_bg'],
            'type'        => $data['type'],
            'options'     => $options ?: null,
            'is_required' => $request->boolean('is_required'),
            'is_visible'  => $request->boolean('is_visible', true),
            'is_system'   => false,
            'sort_order'  => $sort,
        ]);

        ActivityLogger::log('custom_data', 'field_created',
            "Created field {$field->label_en}", $field);

        return redirect()->route('admin.custom-data.index')
            ->with('success', __('flash.custom_field_created'));
    }

    public function updateField(Request $request, CustomField $customField)
    {
        $data = $request->validate([
            'label_en'    => 'required|string|max:191',
            'label_bg'    => 'required|string|max:191',
            'options_en'  => 'nullable|string',
            'options_bg'  => 'nullable|string',
            'is_required' => 'boolean',
            'is_visible'  => 'boolean',
        ]);

        $options = $customField->options;
        if (in_array($customField->type, ['select', 'checkbox']) && !empty(trim($data['options_en'] ?? ''))) {
            $options = $this->parseMultilingualOptions($data['options_en'], $data['options_bg'] ?? '');
        }

        $customField->update([
            'label_en'    => $data['label_en'],
            'label_bg'    => $data['label_bg'],
            'options'     => $options ?: null,
            'is_required' => $request->boolean('is_required'),
            'is_visible'  => $request->boolean('is_visible'),
        ]);

        ActivityLogger::log('custom_data', 'field_updated',
            "Updated field {$customField->label_en}", $customField);

        return redirect()->route('admin.custom-data.index')
            ->with('success', __('flash.custom_field_updated'));
    }

    private function parseMultilingualOptions(string $en, string $bg): array
    {
        $enLines = array_values(array_filter(array_map('trim', explode("\n", $en))));
        $bgLines = array_values(array_filter(array_map('trim', explode("\n", $bg))));

        return array_map(fn($i) => [
            'en' => $enLines[$i] ?? '',
            'bg' => $bgLines[$i] ?? $enLines[$i] ?? '',
        ], array_keys($enLines));
    }

    public function destroyField(CustomField $customField)
    {
        if ($customField->is_system) {
            abort(403, 'System fields cannot be deleted.');
        }

        $label = $customField->label_en;
        $customField->delete();

        ActivityLogger::log('custom_data', 'field_deleted',
            "Deleted field {$label}");

        return redirect()->route('admin.custom-data.index')
            ->with('success', __('flash.custom_field_deleted'));
    }
}
