<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Task $task): RedirectResponse
    {
        $request->validate([
            'body' => ['required','string'],
        ]);

        $task->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->body,
        ]);

        return redirect()->route('tasks.show', $task)->with('status','comment-added');
    }
}