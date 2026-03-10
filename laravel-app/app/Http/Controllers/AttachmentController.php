<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        if (!$this->canAccessTask($task, $user->id, $user->role)) {
            abort(403);
        }

        $request->validate([
            'file' => ['required','file','max:10240'], // 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments');

        $attachment = $task->attachments()->create([
            'user_id' => $user->id,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        $attachment->load('user:id,name');

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($attachment, 201);
        }

        return redirect()->route('tasks.show', $task)->with('status','attachment-added');
    }

    public function show(Request $request, Attachment $attachment): JsonResponse
    {
        $user = $request->user();

        if (!$this->canAccessTask($attachment->task, $user->id, $user->role)) {
            abort(403);
        }

        $attachment->load('user:id,name');

        return response()->json($attachment);
    }

    public function download(Request $request, Attachment $attachment): StreamedResponse
    {
        $user = $request->user();

        if (!$this->canAccessTask($attachment->task, $user->id, $user->role)) {
            abort(403);
        }

        return Storage::download($attachment->path, $attachment->filename);
    }

    public function destroy(Request $request, Attachment $attachment): JsonResponse
    {
        $user = $request->user();

        if (!$this->canDeleteAttachment($attachment, $user->id, $user->role)) {
            abort(403);
        }

        if (Storage::exists($attachment->path)) {
            Storage::delete($attachment->path);
        }

        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted successfully.']);
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

    private function canDeleteAttachment(Attachment $attachment, int $userId, string $role): bool
    {
        if (in_array($role, ['admin', 'manager'], true)) {
            return true;
        }

        return (int) $attachment->user_id === $userId;
    }
}