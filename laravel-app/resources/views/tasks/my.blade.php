@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">My Tasks</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Title</th>
                            <th class="px-4 py-2">Priority</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Due</th>
                            <th class="px-4 py-2">Creator</th>
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
                            <td class="px-4 py-2">{{ $task->creator->name }}</td>
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