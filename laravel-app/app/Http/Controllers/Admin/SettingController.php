<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReminderRule;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Throwable;

class SettingController extends Controller
{
    public function branding(): JsonResponse
    {
        $appName = Setting::where('key', 'app_name')->value('value') ?: config('app.name', 'TaskApp');
        $logoPath = Setting::where('key', 'app_logo_path')->value('value');

        return response()->json([
            'app_name' => $appName,
            'app_logo_url' => $logoPath ? Storage::url($logoPath) : null,
        ]);
    }

    public function updateBranding(Request $request): JsonResponse
    {
        $data = $request->validate([
            'app_name' => ['required', 'string', 'max:120'],
            'app_logo' => ['nullable', 'image', 'max:2048'],
        ]);

        Setting::updateOrCreate(
            ['key' => 'app_name'],
            ['value' => $data['app_name']]
        );

        if ($request->hasFile('app_logo')) {
            $oldLogoPath = Setting::where('key', 'app_logo_path')->value('value');
            if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                Storage::disk('public')->delete($oldLogoPath);
            }

            $storedPath = $request->file('app_logo')->store('app-logos', 'public');

            Setting::updateOrCreate(
                ['key' => 'app_logo_path'],
                ['value' => $storedPath]
            );
        }

        $logoPath = Setting::where('key', 'app_logo_path')->value('value');

        return response()->json([
            'message' => 'Branding settings saved successfully.',
            'app_name' => Setting::where('key', 'app_name')->value('value') ?: config('app.name', 'TaskApp'),
            'app_logo_url' => $logoPath ? Storage::url($logoPath) : null,
        ]);
    }

    public function email(): JsonResponse
    {
        return response()->json([
            'from_name' => Setting::where('key', 'mail_from_name')->value('value') ?: config('mail.from.name'),
            'from_address' => Setting::where('key', 'mail_from_address')->value('value') ?: config('mail.from.address'),
            'smtp_host' => Setting::where('key', 'mail_smtp_host')->value('value') ?: config('mail.mailers.smtp.host'),
            'smtp_port' => (int) (Setting::where('key', 'mail_smtp_port')->value('value') ?: config('mail.mailers.smtp.port')),
            'smtp_username' => Setting::where('key', 'mail_smtp_username')->value('value') ?: config('mail.mailers.smtp.username'),
            'smtp_encryption' => Setting::where('key', 'mail_smtp_encryption')->value('value') ?: 'tls',
            'has_smtp_password' => !empty(Setting::where('key', 'mail_smtp_password')->value('value') ?: config('mail.mailers.smtp.password')),
        ]);
    }

    public function updateEmail(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from_name' => ['required', 'string', 'max:120'],
            'from_address' => ['required', 'email', 'max:190'],
            'smtp_host' => ['required', 'string', 'max:190'],
            'smtp_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'smtp_username' => ['nullable', 'string', 'max:190'],
            'smtp_password' => ['nullable', 'string', 'max:190'],
            'smtp_encryption' => ['nullable', 'in:tls,ssl,none'],
        ]);

        $map = [
            'mail_from_name' => $data['from_name'],
            'mail_from_address' => $data['from_address'],
            'mail_smtp_host' => $data['smtp_host'],
            'mail_smtp_port' => (string) $data['smtp_port'],
            'mail_smtp_username' => $data['smtp_username'] ?? '',
            'mail_smtp_encryption' => $data['smtp_encryption'] ?? 'tls',
        ];

        foreach ($map as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        if (array_key_exists('smtp_password', $data) && $data['smtp_password'] !== null && $data['smtp_password'] !== '') {
            Setting::updateOrCreate(['key' => 'mail_smtp_password'], ['value' => $data['smtp_password']]);
        }

        return response()->json([
            'message' => 'Email settings saved successfully.',
            'from_name' => $data['from_name'],
            'from_address' => $data['from_address'],
            'smtp_host' => $data['smtp_host'],
            'smtp_port' => (int) $data['smtp_port'],
            'smtp_username' => $data['smtp_username'] ?? '',
            'smtp_encryption' => $data['smtp_encryption'] ?? 'tls',
            'has_smtp_password' => !empty(Setting::where('key', 'mail_smtp_password')->value('value')),
        ]);
    }

    public function testEmail(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'to' => ['required', 'email', 'max:190'],
        ]);

        $fromName = Setting::where('key', 'mail_from_name')->value('value') ?: config('mail.from.name');
        $fromAddress = Setting::where('key', 'mail_from_address')->value('value') ?: config('mail.from.address');
        $smtpHost = Setting::where('key', 'mail_smtp_host')->value('value') ?: config('mail.mailers.smtp.host');
        $smtpPort = (int) (Setting::where('key', 'mail_smtp_port')->value('value') ?: config('mail.mailers.smtp.port'));
        $smtpUsername = Setting::where('key', 'mail_smtp_username')->value('value') ?: config('mail.mailers.smtp.username');
        $smtpPassword = Setting::where('key', 'mail_smtp_password')->value('value') ?: config('mail.mailers.smtp.password');
        $smtpEncryption = Setting::where('key', 'mail_smtp_encryption')->value('value') ?: 'tls';

        config([
            'mail.default' => 'smtp',
            'mail.from.address' => $fromAddress,
            'mail.from.name' => $fromName,
            'mail.mailers.smtp.host' => $smtpHost,
            'mail.mailers.smtp.port' => $smtpPort,
            'mail.mailers.smtp.username' => $smtpUsername,
            'mail.mailers.smtp.password' => $smtpPassword,
            'mail.mailers.smtp.encryption' => $smtpEncryption === 'none' ? null : $smtpEncryption,
        ]);

        try {
            Mail::mailer('smtp')->raw('TaskApp SMTP test email. If you received this message, SMTP configuration is working.', function ($message) use ($payload, $fromAddress, $fromName) {
                $message->to($payload['to'])
                    ->from($fromAddress, $fromName)
                    ->subject('TaskApp SMTP Test');
            });
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'SMTP test failed: '.$e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'SMTP test email sent successfully.',
        ]);
    }

    public function security(Request $request): JsonResponse
    {
        $enforceHttps = filter_var(Setting::where('key', 'enforce_https')->value('value') ?? false, FILTER_VALIDATE_BOOLEAN);

        return response()->json([
            'timezone' => $request->user()->timezone ?: config('app.timezone', 'UTC'),
            'enforce_https' => $enforceHttps,
        ]);
    }

    public function updateSecurity(Request $request): JsonResponse
    {
        $data = $request->validate([
            'timezone' => ['required', 'timezone'],
            'enforce_https' => ['required', 'boolean'],
        ]);

        $request->user()->update([
            'timezone' => $data['timezone'],
        ]);

        Setting::updateOrCreate(
            ['key' => 'enforce_https'],
            ['value' => $data['enforce_https'] ? '1' : '0']
        );

        Cache::forget('setting_enforce_https');

        return response()->json([
            'message' => 'Security settings saved successfully.',
            'timezone' => $request->user()->timezone,
            'enforce_https' => $data['enforce_https'],
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }

    public function reminderRule(Request $request): JsonResponse
    {
        $rule = ReminderRule::where('user_id', $request->user()->id)
            ->whereNull('task_id')
            ->latest('id')
            ->first();

        return response()->json([
            'frequency' => $rule?->frequency ?? 'daily',
            'every_n' => $rule?->every_n ?? 2,
            'weekdays_only' => $rule?->weekdays_only ?? false,
            'active' => $rule?->active ?? true,
        ]);
    }

    public function updateReminderRule(Request $request): JsonResponse
    {
        $data = $request->validate([
            'frequency' => ['required', 'in:daily,weekly,every_n_days'],
            'every_n' => ['nullable', 'integer', 'min:1', 'max:365'],
            'weekdays_only' => ['required', 'boolean'],
            'active' => ['required', 'boolean'],
        ]);

        if ($data['frequency'] === 'every_n_days' && empty($data['every_n'])) {
            return response()->json([
                'message' => 'The every_n field is required for every N days frequency.',
            ], 422);
        }

        $rule = ReminderRule::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'task_id' => null,
            ],
            [
                'frequency' => $data['frequency'],
                'every_n' => $data['frequency'] === 'every_n_days' ? $data['every_n'] : null,
                'weekdays_only' => $data['weekdays_only'],
                'active' => $data['active'],
            ]
        );

        return response()->json([
            'message' => 'Reminder rule saved successfully.',
            'frequency' => $rule->frequency,
            'every_n' => $rule->every_n,
            'weekdays_only' => $rule->weekdays_only,
            'active' => $rule->active,
        ]);
    }

    public function assigneeReminderRules(): JsonResponse
    {
        $assignees = User::query()
            ->whereIn('role', ['manager', 'member'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        $rulesByUser = ReminderRule::query()
            ->whereNull('task_id')
            ->whereIn('user_id', $assignees->pluck('id'))
            ->get()
            ->keyBy('user_id');

        return response()->json([
            'assignees' => $assignees->map(function (User $assignee) use ($rulesByUser) {
                $rule = $rulesByUser->get($assignee->id);

                return [
                    'id' => $assignee->id,
                    'name' => $assignee->name,
                    'email' => $assignee->email,
                    'role' => $assignee->role,
                    'rule' => [
                        'frequency' => $rule?->frequency ?? 'daily',
                        'every_n' => $rule?->every_n ?? 2,
                        'weekdays_only' => $rule?->weekdays_only ?? false,
                        'active' => $rule?->active ?? true,
                    ],
                ];
            })->values(),
        ]);
    }

    public function updateAssigneeReminderRule(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'frequency' => ['required', 'in:daily,weekly,every_n_days'],
            'every_n' => ['nullable', 'integer', 'min:1', 'max:365'],
            'weekdays_only' => ['required', 'boolean'],
            'active' => ['required', 'boolean'],
        ]);

        if ($data['frequency'] === 'every_n_days' && empty($data['every_n'])) {
            return response()->json([
                'message' => 'The every_n field is required for every N days frequency.',
            ], 422);
        }

        $assignee = User::query()
            ->whereKey($data['user_id'])
            ->whereIn('role', ['manager', 'member'])
            ->where('is_active', true)
            ->first();

        if (! $assignee) {
            return response()->json([
                'message' => 'Selected assignee is not eligible for reminder configuration.',
            ], 422);
        }

        $rule = ReminderRule::updateOrCreate(
            [
                'user_id' => $assignee->id,
                'task_id' => null,
            ],
            [
                'frequency' => $data['frequency'],
                'every_n' => $data['frequency'] === 'every_n_days' ? $data['every_n'] : null,
                'weekdays_only' => $data['weekdays_only'],
                'active' => $data['active'],
            ]
        );

        return response()->json([
            'message' => 'Assignee reminder rule saved successfully.',
            'user_id' => $assignee->id,
            'rule' => [
                'frequency' => $rule->frequency,
                'every_n' => $rule->every_n,
                'weekdays_only' => $rule->weekdays_only,
                'active' => $rule->active,
            ],
        ]);
    }
}
