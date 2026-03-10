<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, Task $task): JsonResponse
    {
        $user = $request->user();

        if (!$this->canAccessTask($task, $user->id, $user->role)) {
            abort(403);
        }

        $comments = $task->comments()
            ->with('user:id,name')
            ->latest()
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        if (!$this->canAccessTask($task, $user->id, $user->role)) {
            abort(403);
        }

        $request->validate([
            'body' => ['required','string'],
        ]);

        $comment = $task->comments()->create([
            'user_id' => $user->id,
            'body' => $request->body,
        ]);

        $comment->load('user:id,name');

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($comment, 201);
        }

        return redirect()->route('tasks.show', $task)->with('status','comment-added');
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        $user = $request->user();

        if (!$this->canModifyComment($comment, $user->id, $user->role)) {
            abort(403);
        }

        $request->validate([
            'body' => ['required', 'string'],
        ]);

        $comment->update([
            'body' => $request->body,
        ]);

        $comment->load('user:id,name');

        return response()->json($comment);
    }

    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        $user = $request->user();

        if (!$this->canModifyComment($comment, $user->id, $user->role)) {
            abort(403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.']);
    }

    private function canAccessTask(Task $task, int $userId, string $role): bool
    {
        if (in_array($role, ['admin', 'manager'], true)) {
            return true;
        }

        if ($task->creator_id === $userId) {
            return true;
        }

        return $task->assignees()->where('user_id', $userId)->exists();
    }

    private function canModifyComment(Comment $comment, int $userId, string $role): bool
    {
        if (in_array($role, ['admin', 'manager'], true)) {
            return true;
        }

        return $comment->user_id === $userId;
    }
}