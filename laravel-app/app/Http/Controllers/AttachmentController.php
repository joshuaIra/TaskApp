<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Task $task): RedirectResponse
    {
        $request->validate([
            'file' => ['required','file','max:10240'], // 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments');

        $task->attachments()->create([
            'user_id' => $request->user()->id,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        return redirect()->route('tasks.show', $task)->with('status','attachment-added');
    }

    public function download(Attachment $attachment)
    {
        return Storage::download($attachment->path, $attachment->filename);
    }
}