@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">Tasks</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <a href="{{ route('tasks.create') }}" class="mb-4 inline-block bg-blue-500 text-white px-4 py-2 rounded">New Task</a>
                <form method="GET" class="mb-4">
                    <div class="flex flex-wrap gap-4">
                        <select name="status" class="border rounded px-3 py-2">
                            <option value="">All statuses</option>
                            @foreach(['pending','in_progress','completed'] as $s)
                                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ str_replace('_',' ', ucfirst($s)) }}</option>
                            @endforeach
                        </select>
                        <select name="priority" class="border rounded px-3 py-2">
                            <option value="">All priorities</option>
                            @foreach(['low','medium','high'] as $p)
                                <option value="{{ $p }}" {{ request('priority') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                        <select name="assignee" class="border rounded px-3 py-2">
                            <option value="">Any assignee</option>
                            @foreach($assignees as $u)
                                <option value="{{ $u->id }}" {{ request('assignee') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="due_before" value="{{ request('due_before') }}" class="border rounded px-3 py-2" placeholder="Due before" />
                        <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded">Filter</button>
                    </div>
                </form>
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Title</th>
                            <th class="px-4 py-2">Priority</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Due</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $task->id }}</td>
                            <td class="px-4 py-2"><a href="{{ route('tasks.show', $task) }}" class="text-blue-600">{{ $task->title }}</a></td>
                            <td class="px-4 py-2">{{ ucfirst($task->priority) }}</td>
                            <td class="px-4 py-2">{{ str_replace('_',' ', ucfirst($task->status)) }}</td>
                            <td class="px-4 py-2">{{ optional($task->due_at)->format('Y-m-d') }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('tasks.edit', $task) }}" class="text-blue-600">Edit</a>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 ml-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection