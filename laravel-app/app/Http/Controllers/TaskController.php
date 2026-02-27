<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $query = Task::query()->with('creator', 'assignees');

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
        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        return view('tasks.create');
    }

    public function store(TaskRequest $request): RedirectResponse
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
        return redirect()->route('tasks.index')->with('status', 'task-created');
    }

    public function show(Task $task): View
    {
        $task->load('comments', 'attachments', 'assignees');
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse
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
        return redirect()->route('tasks.index')->with('status', 'task-updated');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('status', 'task-deleted');
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
