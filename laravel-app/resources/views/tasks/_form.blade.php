@csrf

@php
    $taskAssigneeIds = isset($task) ? $task->assignees->pluck('id')->all() : [];
@endphp

<div class="mb-4">
    <label class="block text-gray-700">Title</label>
    <input type="text" name="title" value="{{ old('title', $task->title ?? '') }}" class="w-full border rounded px-3 py-2" required>
</div>

<div class="mb-4">
    <label class="block text-gray-700">Description</label>
    <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description', $task->description ?? '') }}</textarea>
</div>

<div class="mb-4">
    <label class="block text-gray-700">Priority</label>
    <select name="priority" class="w-full border rounded px-3 py-2" required>
        @foreach(['low','medium','high'] as $prio)
            <option value="{{ $prio }}" {{ (old('priority', $task->priority ?? '') === $prio) ? 'selected' : '' }}>{{ ucfirst($prio) }}</option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label class="block text-gray-700">Status</label>
    <select name="status" class="w-full border rounded px-3 py-2" required>
        @foreach(['pending','in_progress','completed'] as $status)
            <option value="{{ $status }}" {{ (old('status', $task->status ?? '') === $status) ? 'selected' : '' }}>{{ str_replace('_',' ', ucfirst($status)) }}</option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label class="block text-gray-700">Due Date</label>
    <input type="date" name="due_at" value="{{ old('due_at', isset($task->due_at) ? $task->due_at->format('Y-m-d') : '') }}" class="w-full border rounded px-3 py-2">
</div>

<div class="mb-4">
    <label class="block text-gray-700">Assignees</label>
    <select name="assignees[]" multiple class="w-full border rounded px-3 py-2">
        @foreach(
            \App\Models\User::orderBy('name')->get() as $user
        )
            <option value="{{ $user->id }}" {{ (collect(old('assignees', $taskAssigneeIds))->contains($user->id)) ? 'selected' : '' }}>{{ $user->name }}</option>
        @endforeach
    </select>
</div>