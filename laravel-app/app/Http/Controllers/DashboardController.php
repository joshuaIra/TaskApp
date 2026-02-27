<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\Task;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        // build base query with optional filters
        $query = Task::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('assignee')) {
            $query->whereHas('assignees', fn($q) => $q->where('user_id', $request->assignee));
        }
        if ($request->filled('due_before')) {
            $query->where('due_at', '<=', $request->due_before);
        }

        // basic counts for KPI cards (apply filters except overdue condition)
        $totals = [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'overdue' => (clone $query)->where('due_at', '<', now())->where('status', '!=', 'completed')->count(),
        ];

        // data for charts (use filtered query)
        $statusData = (clone $query)
            ->select('status', DB::raw('count(*) as cnt'))
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();
        $statusDefaults = ['pending' => 0, 'in_progress' => 0, 'completed' => 0];
        $statusData = array_merge($statusDefaults, $statusData);

        $priorityData = (clone $query)
            ->select('priority', DB::raw('count(*) as cnt'))
            ->groupBy('priority')
            ->pluck('cnt', 'priority')
            ->toArray();
        $priorityDefaults = ['low' => 0, 'medium' => 0, 'high' => 0];
        $priorityData = array_merge($priorityDefaults, $priorityData);

        return view('dashboard', compact('totals', 'statusData', 'priorityData'));
    }
}
