<?php

use Illuminate\Support\Facades\Route;

Route::name('api.')->middleware(['web', 'auth'])->group(function () {
    // Task endpoints
    Route::get('tasks', [\App\Http\Controllers\TaskController::class, 'index'])->name('tasks.index');
    Route::get('tasks/{task}', [\App\Http\Controllers\TaskController::class, 'show'])->name('tasks.show');
    Route::get('my-tasks', [\App\Http\Controllers\TaskController::class, 'myTasks'])->name('tasks.my');

    Route::middleware('role:admin,manager')->group(function () {
        Route::get('assignees', [\App\Http\Controllers\TaskController::class, 'assignees'])->name('tasks.assignees');
        Route::post('tasks', [\App\Http\Controllers\TaskController::class, 'store'])->name('tasks.store');
        Route::post('tasks/import', [\App\Http\Controllers\TaskController::class, 'import'])->name('tasks.import');
        Route::put('tasks/{task}', [\App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update');
        Route::delete('tasks/{task}', [\App\Http\Controllers\TaskController::class, 'destroy'])->name('tasks.destroy');
    });

    Route::patch('tasks/{task}/progress', [\App\Http\Controllers\TaskController::class, 'updateProgress'])->name('tasks.progress.update');

    // Comments endpoints
    Route::post('tasks/{task}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::get('tasks/{task}/comments', [\App\Http\Controllers\CommentController::class, 'index'])->name('comments.index');
    Route::put('comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');

    // Attachments endpoints
    Route::post('tasks/{task}/attachments', [\App\Http\Controllers\AttachmentController::class, 'store'])->name('attachments.store');
    Route::get('attachments/{attachment}', [\App\Http\Controllers\AttachmentController::class, 'show'])->name('attachments.show');
    Route::get('attachments/{attachment}/download', [\App\Http\Controllers\AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('attachments/{attachment}', [\App\Http\Controllers\AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // Notifications endpoints
    Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Admin endpoints
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
        Route::post('users/{user}/toggle', [\App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle');
        Route::get('settings/branding', [\App\Http\Controllers\Admin\SettingController::class, 'branding'])->name('settings.branding');
        Route::post('settings/branding', [\App\Http\Controllers\Admin\SettingController::class, 'updateBranding'])->name('settings.branding.update');
        Route::get('settings/email', [\App\Http\Controllers\Admin\SettingController::class, 'email'])->name('settings.email');
        Route::post('settings/email', [\App\Http\Controllers\Admin\SettingController::class, 'updateEmail'])->name('settings.email.update');
        Route::post('settings/email/test', [\App\Http\Controllers\Admin\SettingController::class, 'testEmail'])->name('settings.email.test');
        Route::get('settings/security', [\App\Http\Controllers\Admin\SettingController::class, 'security'])->name('settings.security');
        Route::post('settings/security', [\App\Http\Controllers\Admin\SettingController::class, 'updateSecurity'])->name('settings.security.update');
        Route::post('settings/security/password', [\App\Http\Controllers\Admin\SettingController::class, 'updatePassword'])->name('settings.security.password');
        Route::get('settings/reminders', [\App\Http\Controllers\Admin\SettingController::class, 'reminderRule'])->name('settings.reminders');
        Route::post('settings/reminders', [\App\Http\Controllers\Admin\SettingController::class, 'updateReminderRule'])->name('settings.reminders.update');
        Route::get('settings/reminders/assignees', [\App\Http\Controllers\Admin\SettingController::class, 'assigneeReminderRules'])->name('settings.reminders.assignees');
        Route::post('settings/reminders/assignees', [\App\Http\Controllers\Admin\SettingController::class, 'updateAssigneeReminderRule'])->name('settings.reminders.assignees.update');
    });
});
