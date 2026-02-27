@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">Task: {{ $task->title }}</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <p><strong>Description:</strong> {{ $task->description }}</p>
                <p><strong>Priority:</strong> {{ ucfirst($task->priority) }}</p>
                <p><strong>Status:</strong> {{ str_replace('_',' ', ucfirst($task->status)) }}</p>
                <p><strong>Due:</strong> {{ optional($task->due_at)->format('Y-m-d') }}</p>
                <p><strong>Creator:</strong> {{ $task->creator->name }}</p>
                <p><strong>Assignees:</strong> {{ $task->assignees->pluck('name')->join(', ') }}</p>

                <!-- comments -->
                <div class="mt-6">
                    <h3 class="font-semibold">Comments</h3>
                    @foreach($task->comments as $comment)
                        <div class="border rounded p-2 my-2">
                            <p>{{ $comment->body }}</p>
                            <p class="text-xs text-gray-500">by {{ $comment->user->name }} at {{ $comment->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    @endforeach
                    <form action="{{ route('tasks.comments.store', $task) }}" method="POST">
                        @csrf
                        <textarea name="body" class="w-full border rounded px-3 py-2" required></textarea>
                        <button class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Add Comment</button>
                    </form>
                </div>

                <!-- attachments -->
                <div class="mt-6">
                    <h3 class="font-semibold">Attachments</h3>
                    @foreach($task->attachments as $attach)
                        <div class="my-2">
                            <a href="{{ route('attachments.download', $attach) }}" class="text-blue-600">{{ $attach->filename }}</a>
                            <span class="text-xs text-gray-500">({{ number_format($attach->size/1024,2) }} KB)</span>
                        </div>
                    @endforeach
                    <form action="{{ route('tasks.attachments.store', $task) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" required class="border rounded px-3 py-2" />
                        <button class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection