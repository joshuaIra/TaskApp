<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Task Assigned</title>
</head>
<body>
    <p>Hello {{ $assignee->name }},</p>

    <p>You have been assigned a new task by {{ $assignedBy->name }}.</p>

    <p><strong>Task:</strong> {{ $task->title }}</p>

    @if(!empty($task->description))
        <p><strong>Description:</strong> {{ $task->description }}</p>
    @endif

    @if(!empty($task->due_at))
        <p><strong>Due Date:</strong> {{ \Illuminate\Support\Carbon::parse($task->due_at)->format('M d, Y H:i') }}</p>
    @endif

    <p><a href="{{ url('/tasks/' . $task->id) }}">View Task</a></p>

    <p>Thanks.</p>
</body>
</html>
