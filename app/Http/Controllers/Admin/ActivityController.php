<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $query  = Activity::with('causer')
            ->latest()
            ->when($request->causer_id, fn ($q) => $q->where('causer_id', $request->causer_id))
            ->when($request->log_name,  fn ($q) => $q->where('log_name', $request->log_name))
            ->when($search, fn ($q) => $q->where('description', 'like', "%{$search}%"));

        $activities = $query->paginate(30)->withQueryString();

        return view('admin.activity', compact('activities', 'search'));
    }
}
