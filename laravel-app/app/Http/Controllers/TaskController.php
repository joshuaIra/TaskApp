<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Mail\TaskAssignedMail;
use App\Models\Setting;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,manager')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        $isApiRequest = $request->expectsJson() || $request->is('api/*');

        $query = Task::query()
            ->select(['id', 'title', 'priority', 'status', 'progress_percentage', 'due_at', 'creator_id'])
            ->with([
                'creator:id,name',
                'assignees:id,name',
            ]);

        if ($user->role === 'member') {
            $query->whereHas('assignees', fn($q) => $q->where('users.id', $user->id));
        }

        // filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('assignee')) {
            $query->whereHas('assignees', fn($q) => $q->where('users.id', $request->assignee));
        }
        if ($request->filled('due_before')) {
            $query->where('due_at', '<=', $request->due_before);
        }

        if ($isApiRequest) {
            $tasks = $query->orderBy('due_at')->get();
            return response()->json($tasks);
        }

        $tasks = $query->orderBy('due_at')->paginate(15);

        $assignees = $user->role === 'member'
            ? User::select(['id', 'name'])->where('id', $user->id)->get()
            : Cache::remember('task_filter_assignees', now()->addMinutes(5), function () {
                return User::select(['id', 'name'])->orderBy('name')->get();
            });

        return view('tasks.index', compact('tasks', 'assignees'));
    }

    public function create(): View
    {
        return view('tasks.create');
    }

    public function assignees(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));

        $query = User::query()
            ->select(['id', 'name', 'email', 'role'])
            ->where('is_active', true)
            ->where('role', 'member');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'assignees' => $query->orderBy('name')->limit(50)->get(),
        ]);
    }

    public function store(TaskRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $creator = $request->user();
        $data['creator_id'] = $creator->id;
        $task = Task::create($data);
        $assigneeIds = collect($data['assignees'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();

        if (!empty($assigneeIds)) {
            $task->assignees()->sync($this->buildAssigneeSyncPayload($task, $assigneeIds, $creator->id));
            Log::info('Task created with assignees.', [
                'task_id' => $task->id,
                'creator_id' => $creator->id,
                'assignee_ids' => $assigneeIds,
            ]);
            $this->notifyAssignees($task, $creator);
        } else {
            Log::warning('Task created without assignees.', [
                'task_id' => $task->id,
                'creator_id' => $creator->id,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json($task->load(['creator:id,name', 'assignees:id,name']), 201);
        }

        return redirect('/tasks')->with('status', 'task-created');
    }

    public function show(Task $task): View|JsonResponse
    {
        $user = request()->user();
        if ($user && $user->role === 'member' && !$task->assignees()->where('users.id', $user->id)->exists()) {
            abort(403);
        }

        $task->load([
            'creator:id,name',
            'assignees:id,name',
            'comments' => fn($query) => $query->with('user:id,name')->latest(),
            'attachments' => fn($query) => $query->with('user:id,name')->latest(),
        ]);

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($task);
        }

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $task->update($data);
        if (isset($data['assignees'])) {
            $normalizedAssigneeIds = collect($data['assignees'])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
            $previousAssigneeIds = $task->assignees()->pluck('users.id')->all();
            $task->assignees()->sync(
                $this->buildAssigneeSyncPayload($task, $normalizedAssigneeIds, $request->user()?->id)
            );

            Log::info('Task assignees updated.', [
                'task_id' => $task->id,
                'actor_id' => $request->user()?->id,
                'assignee_ids' => $normalizedAssigneeIds,
            ]);

            $this->notifyAssignees($task, $request->user(), $previousAssigneeIds);
        }

        if ($request->expectsJson()) {
            return response()->json($task->load(['creator:id,name', 'assignees:id,name']));
        }

        return redirect('/tasks')->with('status', 'task-updated');
    }

    public function updateProgress(Request $request, Task $task): JsonResponse
    {
        $user = $request->user();

        if (!$user || !$user->is_active) {
            abort(403);
        }

        $isManagerOrAdmin = in_array($user->role, ['admin', 'manager'], true);
        $isAssignedMember = $user->role === 'member' && $task->assignees()->where('user_id', $user->id)->exists();

        if (!$isManagerOrAdmin && !$isAssignedMember) {
            abort(403);
        }

        $data = $request->validate([
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $progressPercentage = (int) $data['progress_percentage'];
        $task->progress_percentage = $progressPercentage;

        if ($progressPercentage >= 100) {
            $task->status = 'completed';
        } elseif ($progressPercentage > 0 && $task->status === 'pending') {
            $task->status = 'in_progress';
        }

        $task->save();

        return response()->json([
            'message' => 'Task progress updated successfully.',
            'task' => $task->load(['creator:id,name', 'assignees:id,name']),
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $uploadedFile = $request->file('file');
        $extension = strtolower((string) $uploadedFile->getClientOriginalExtension());

        if ($extension === '') {
            $originalName = (string) $uploadedFile->getClientOriginalName();
            $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
        }

        if (!in_array($extension, ['xlsx', 'csv'], true)) {
            return response()->json([
                'message' => 'Unsupported file type. Please upload a .xlsx or .csv file.',
            ], 422);
        }

        $creator = $request->user();
        $rows = $this->readImportRows($uploadedFile);

        if (empty($rows) || count($rows) < 2) {
            return response()->json([
                'message' => 'Import file is empty. Add a header row and at least one task row.',
            ], 422);
        }

        $headerRow = array_shift($rows);
        $headers = [];
        foreach ($headerRow as $column => $value) {
            $headers[$column] = strtolower(trim((string) $value));
        }

        $created = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $rowIndex => $row) {
                $lineNumber = $rowIndex + 2;
                $mapped = $this->mapTaskImportRow($row, $headers);

                $title = trim((string) ($mapped['title'] ?? $mapped['smm_recommendations'] ?? ''));
                if ($title === '') {
                    $skipped++;
                    $errors[] = "Row {$lineNumber}: title is required.";
                    continue;
                }

                $priority = in_array(($mapped['priority'] ?? ''), ['low', 'medium', 'high'], true)
                    ? $mapped['priority']
                    : 'medium';

                $rawStatusValue = trim((string) ($mapped['status'] ?? ''));
                $progressPercentage = null;
                $status = 'pending';

                if ($rawStatusValue !== '') {
                    if (is_numeric($rawStatusValue)) {
                        $progressPercentage = max(0, min(100, (int) round((float) $rawStatusValue)));
                        if ($progressPercentage >= 100) {
                            $status = 'completed';
                        } elseif ($progressPercentage > 0) {
                            $status = 'in_progress';
                        }
                    } elseif (in_array($rawStatusValue, ['pending', 'in_progress', 'completed'], true)) {
                        $status = $rawStatusValue;
                    }
                }

                $dueAt = $this->normalizeDueAtValue(
                    $mapped['due_at'] ?? $mapped['initial_date_completion_date'] ?? null
                );

                $task = Task::create([
                    'title' => $title,
                    'description' => trim((string) ($mapped['description'] ?? $mapped['comments'] ?? '')) ?: null,
                    'priority' => $priority,
                    'status' => $status,
                    'progress_percentage' => $progressPercentage ?? 0,
                    'due_at' => $dueAt,
                    'creator_id' => $creator->id,
                ]);

                $responsiblePersonValue = $mapped['responsible_person'] ?? $mapped['assignees'] ?? '';
                $assigneeIds = $this->resolveAssigneeIdsFromImport($responsiblePersonValue);
                if (!empty($assigneeIds)) {
                    $task->assignees()->sync($this->buildAssigneeSyncPayload($task, $assigneeIds, $creator->id));
                    $this->notifyAssignees($task, $creator);
                }

                $created++;
            }

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('Task import failed.', [
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Task import failed. Please check the file format and try again.',
            ], 500);
        }

        return response()->json([
            'message' => 'Tasks imported successfully.',
            'created' => $created,
            'skipped' => $skipped,
            'errors' => $errors,
            'expected_columns' => [
                'SMM & Recommendations',
                'Initial date/ Completion Date',
                'Responsible person',
                'Comments (optional)',
                'Status (%) (optional)',
            ],
        ]);
    }

    private function notifyAssignees(Task $task, User $creator, array $existingAssigneeIds = []): void
    {
        $this->configureMailRuntime();

        $assignees = $task->assignees()->whereNotNull('users.email');

        if (!empty($existingAssigneeIds)) {
            $assignees->whereNotIn('users.id', $existingAssigneeIds);
        }

        $targets = $assignees->get()->unique('id');

        if ($targets->isEmpty()) {
            Log::warning('Task assignment email skipped: no assignees with valid email.', [
                'task_id' => $task->id,
                'existing_assignee_ids' => $existingAssigneeIds,
            ]);
            return;
        }

        foreach ($targets as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'task_assigned',
                'data' => ['message' => "You were assigned to task: {$task->title}", 'task_id' => $task->id],
            ]);

            if (!empty($user->email)) {
                try {
                    Mail::mailer(config('mail.default', 'smtp'))->to($user->email)->send(new TaskAssignedMail($task, $user, $creator));
                    Log::info('Task assignment email sent.', [
                        'task_id' => $task->id,
                        'assignee_id' => $user->id,
                        'assignee_email' => $user->email,
                        'mailer' => config('mail.default'),
                        'smtp_host' => config('mail.mailers.smtp.host'),
                        'from_address' => config('mail.from.address'),
                    ]);
                } catch (Throwable $exception) {
                    Log::error('Failed to send task assignment email.', [
                        'task_id' => $task->id,
                        'assignee_id' => $user->id,
                        'assignee_email' => $user->email,
                        'mailer' => config('mail.default'),
                        'smtp_host' => config('mail.mailers.smtp.host'),
                        'from_address' => config('mail.from.address'),
                        'error' => $exception->getMessage(),
                    ]);
                }
            }
        }
    }

    private function buildAssigneeSyncPayload(Task $task, array $assigneeIds, ?int $assignedById): array
    {
        if (empty($assigneeIds)) {
            return [];
        }

        $normalizedAssigneeIds = collect($assigneeIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $existingAssignments = DB::table('task_user')
            ->where('task_id', $task->id)
            ->whereIn('user_id', $normalizedAssigneeIds)
            ->get(['user_id', 'assigned_at', 'assigned_by'])
            ->keyBy(fn ($row) => (int) $row->user_id);

        $now = now()->toDateTimeString();
        $payload = [];

        foreach ($normalizedAssigneeIds as $assigneeId) {
            $existing = $existingAssignments->get((int) $assigneeId);

            $payload[(int) $assigneeId] = [
                'assigned_at' => $existing?->assigned_at ?? $now,
                'assigned_by' => $existing?->assigned_by ?? $assignedById,
            ];
        }

        return $payload;
    }

    private function configureMailRuntime(): void
    {
        $settings = [];

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settings = Setting::query()
                    ->whereIn('key', [
                        'mail_from_name',
                        'mail_from_address',
                        'mail_smtp_host',
                        'mail_smtp_port',
                        'mail_smtp_username',
                        'mail_smtp_password',
                        'mail_smtp_encryption',
                    ])
                    ->pluck('value', 'key')
                    ->all();
            }
        } catch (Throwable) {
            $settings = [];
        }

        $smtpHost = (string) ($settings['mail_smtp_host'] ?? env('SMTP_HOST', env('MAIL_HOST', config('mail.mailers.smtp.host'))));
        $smtpPort = (int) ($settings['mail_smtp_port'] ?? env('SMTP_PORT', env('MAIL_PORT', config('mail.mailers.smtp.port'))));
        $smtpUsername = (string) ($settings['mail_smtp_username'] ?? env('SMTP_USERNAME', env('MAIL_USERNAME', config('mail.mailers.smtp.username'))));
        $smtpPassword = (string) ($settings['mail_smtp_password'] ?? env('SMTP_PASSWORD', env('MAIL_PASSWORD', config('mail.mailers.smtp.password'))));
        $smtpEncryption = (string) ($settings['mail_smtp_encryption'] ?? env('SMTP_ENCRYPTION', env('MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption', 'tls'))));

        $fromName = (string) ($settings['mail_from_name'] ?? env('FROM_NAME', env('MAIL_FROM_NAME', config('mail.from.name'))));
        $fromAddress = (string) ($settings['mail_from_address'] ?? env('FROM_EMAIL', env('MAIL_FROM_ADDRESS', config('mail.from.address'))));

        if ($fromAddress === '' && $smtpUsername !== '') {
            $fromAddress = $smtpUsername;
        }

        if (str_contains(strtolower($smtpHost), 'gmail.com') && $smtpUsername !== '') {
            $fromAddress = $smtpUsername;
        }

        config([
            'mail.default' => 'smtp',
            'mail.from.address' => $fromAddress,
            'mail.from.name' => $fromName,
            'mail.mailers.smtp.host' => $smtpHost,
            'mail.mailers.smtp.port' => $smtpPort,
            'mail.mailers.smtp.username' => $smtpUsername,
            'mail.mailers.smtp.password' => $smtpPassword,
            'mail.mailers.smtp.scheme' => null,
            'mail.mailers.smtp.encryption' => $smtpEncryption === 'none' ? null : $smtpEncryption,
        ]);
    }

    private function mapTaskImportRow(array $row, array $headers): array
    {
        $mapped = [];

        foreach ($headers as $column => $header) {
            if ($header === '') {
                continue;
            }

            $normalizedHeader = preg_replace('/[^a-z0-9]+/', '_', $header);
            $normalizedHeader = trim((string) $normalizedHeader, '_');
            $mapped[$normalizedHeader] = $row[$column] ?? null;
        }

        return $mapped;
    }

    private function normalizeDueAtValue(mixed $rawDueAt): ?string
    {
        if ($rawDueAt === null || $rawDueAt === '') {
            return null;
        }

        if (is_numeric($rawDueAt)) {
            $excelSerial = (float) $rawDueAt;
            $unixTimestamp = (int) round(($excelSerial - 25569) * 86400);

            if ($unixTimestamp > 0) {
                return date('Y-m-d H:i:s', $unixTimestamp);
            }

            return null;
        }

        $timestamp = strtotime((string) $rawDueAt);
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    private function resolveAssigneeIdsFromImport(mixed $rawAssignees): array
    {
        if ($rawAssignees === null || trim((string) $rawAssignees) === '') {
            return [];
        }

        $tokens = preg_split('/[,;]+/', (string) $rawAssignees) ?: [];
        $identifiers = array_values(array_filter(array_map(fn($token) => trim($token), $tokens)));

        if (empty($identifiers)) {
            return [];
        }

        $users = User::query()
            ->where('is_active', true)
            ->where('role', 'member')
            ->where(function ($query) use ($identifiers) {
                foreach ($identifiers as $identifier) {
                    $query->orWhere('username', $identifier)
                        ->orWhere('email', $identifier)
                        ->orWhere('name', $identifier);
                }
            })
            ->get(['id', 'username', 'email', 'name']);

        return $users->pluck('id')->unique()->values()->all();
    }

    private function readImportRows(UploadedFile $file): array
    {
        $extension = strtolower((string) $file->getClientOriginalExtension());

        if ($extension === 'csv') {
            return $this->readCsvRows($file->getRealPath());
        }

        if ($extension === 'xlsx') {
            return $this->readXlsxRows($file->getRealPath());
        }

        return [];
    }

    private function readCsvRows(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return [];
        }

        while (($data = fgetcsv($handle)) !== false) {
            $mappedRow = [];
            foreach ($data as $index => $value) {
                $mappedRow[$this->indexToColumnLetter($index)] = $value;
            }
            $rows[] = $mappedRow;
        }

        fclose($handle);

        return $rows;
    }

    private function readXlsxRows(string $path): array
    {
        if (!class_exists(\ZipArchive::class)) {
            return [];
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $sharedStrings = $this->parseSharedStrings($sharedStringsXml);
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            return [];
        }

        return $this->parseSheetRows($sheetXml, $sharedStrings);
    }

    private function parseSharedStrings(string $xml): array
    {
        $dom = new \DOMDocument();
        if (!@$dom->loadXML($xml)) {
            return [];
        }

        $strings = [];
        foreach ($dom->getElementsByTagName('si') as $siNode) {
            $textValue = '';
            foreach ($siNode->getElementsByTagName('t') as $textNode) {
                $textValue .= $textNode->textContent;
            }
            $strings[] = $textValue;
        }

        return $strings;
    }

    private function parseSheetRows(string $xml, array $sharedStrings): array
    {
        $dom = new \DOMDocument();
        if (!@$dom->loadXML($xml)) {
            return [];
        }

        $rows = [];

        foreach ($dom->getElementsByTagName('row') as $rowNode) {
            $mappedRow = [];

            foreach ($rowNode->getElementsByTagName('c') as $cellNode) {
                $cellRef = (string) ($cellNode->getAttribute('r') ?? '');
                $column = preg_replace('/\d+/', '', $cellRef);

                if ($column === '') {
                    continue;
                }

                $type = (string) ($cellNode->getAttribute('t') ?? '');
                $valueNode = $cellNode->getElementsByTagName('v')->item(0);
                $value = $valueNode ? (string) $valueNode->textContent : '';

                if ($type === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                } elseif ($type === 'inlineStr') {
                    $inlineNode = $cellNode->getElementsByTagName('t')->item(0);
                    $value = $inlineNode ? (string) $inlineNode->textContent : '';
                }

                $mappedRow[$column] = $value;
            }

            $rows[] = $mappedRow;
        }

        return $rows;
    }

    private function indexToColumnLetter(int $index): string
    {
        $index += 1;
        $letters = '';

        while ($index > 0) {
            $remainder = ($index - 1) % 26;
            $letters = chr(65 + $remainder) . $letters;
            $index = intdiv($index - 1, 26);
        }

        return $letters;
    }

    public function destroy(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $actor = $request->user();

        if (!$actor || !in_array($actor->role, ['admin', 'manager'], true)) {
            abort(403);
        }

        if ($actor->role === 'manager' && $task->status !== 'pending') {
            return response()->json([
                'message' => 'Managers can only delete tasks that are not started yet (pending).',
            ], 403);
        }

        $task->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Task deleted successfully.']);
        }

        return redirect('/tasks')->with('status', 'task-deleted');
    }

    /**
     * Display tasks assigned to the authenticated user.
     */
    public function myTasks(Request $request): View|JsonResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            $tasks = $request->user()->assignedTasks()->with('creator')->orderBy('due_at')->get();
            return response()->json($tasks);
        }

        $tasks = $request->user()->assignedTasks()->with('creator')->paginate(15);
        return view('tasks.my', compact('tasks'));
    }
}
