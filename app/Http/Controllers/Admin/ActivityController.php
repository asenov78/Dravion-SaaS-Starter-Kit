<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->input('search', '');
        $causerId = $request->input('causer_id', '');
        $logName  = $request->input('log_name', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo   = $request->input('date_to', '');

        $activities = Activity::with('causer')
            ->latest()
            ->when($causerId, fn ($q) => $q->where('causer_id', $causerId))
            ->when($logName,  fn ($q) => $q->where('log_name', $logName))
            ->when($search,   fn ($q) => $q->where('description', 'like', "%{$search}%"))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->paginate(30)
            ->withQueryString();

        $logNames = Activity::distinct()->orderBy('log_name')->pluck('log_name');
        $users    = User::orderBy('name')->get(['id', 'name']);

        return view('admin.activity', compact(
            'activities', 'search', 'causerId', 'logName', 'dateFrom', 'dateTo', 'logNames', 'users'
        ));
    }
}
