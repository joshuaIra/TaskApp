<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,manager')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Task::query()
            ->select(['id', 'title', 'priority', 'status', 'due_at', 'creator_id'])
            ->with([
                'creator:id,name',
                'assignees:id,name',
            ]);

        if ($user->role === 'member') {
            $query->whereHas('assignees', fn($q) => $q->where('user_id', $user->id));
        }

        // filters
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

        $tasks = $query->orderBy('due_at')->paginate(15);

        $assignees = $user->role === 'member'
            ? User::select(['id', 'name'])->where('id', $user->id)->get()
            : Cache::remember('task_filter_assignees', now()->addMinutes(5), function () {
                return User::select(['id', 'name'])->orderBy('name')->get();
            });

        return view('tasks.index', compact('tasks', 'assignees'));
    }

    public function create(): View
    {
        return view('tasks.create');
    }

    public function store(TaskRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $data['creator_id'] = $request->user()->id;
        $task = Task::create($data);
        if (!empty($data['assignees'])) {
            $task->assignees()->sync($data['assignees']);
            // notify assignees
            foreach ($task->assignees as $user) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'task_assigned',
                    'data' => ['message' => "You were assigned to task: {$task->title}", 'task_id' => $task->id],
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json($task->load(['creator:id,name', 'assignees:id,name']), 201);
        }

        return redirect('/tasks')->with('status', 'task-created');
    }

    public function show(Task $task): View
    {
        $user = request()->user();
        if ($user && $user->role === 'member' && !$task->assignees()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        $task->load('comments', 'attachments', 'assignees');
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $task->update($data);
        if (isset($data['assignees'])) {
            $task->assignees()->sync($data['assignees']);
            foreach ($task->assignees as $user) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'task_assigned',
                    'data' => ['message' => "You are assigned to task: {$task->title}", 'task_id' => $task->id],
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json($task->load(['creator:id,name', 'assignees:id,name']));
        }

        return redirect('/tasks')->with('status', 'task-updated');
    }

    public function destroy(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $task->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Task deleted successfully.']);
        }

        return redirect('/tasks')->with('status', 'task-deleted');
    }

    /**
     * Display tasks assigned to the authenticated user.
     */
    public function myTasks(Request $request): View
    {
        $tasks = $request->user()->assignedTasks()->with('creator')->paginate(15);
        return view('tasks.my', compact('tasks'));
    }
}
