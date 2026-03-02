<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('spa');
});

Route::view('/contact', 'spa');
Route::view('/privacy', 'spa');
Route::view('/terms', 'spa');
Route::view('/dashboard/admin', 'spa')->middleware(['auth', 'role:admin']);
Route::view('/dashboard/manager', 'spa')->middleware(['auth', 'role:manager']);
Route::view('/dashboard/member', 'spa')->middleware(['auth', 'role:member']);
Route::view('/reports', 'spa')->middleware(['auth', 'role:admin,manager']);
Route::view('/settings', 'spa')->middleware(['auth', 'role:admin']);
Route::view('/tasks', 'spa')->middleware('auth');
Route::view('/tasks/create', 'spa')->middleware(['auth', 'role:admin,manager']);
Route::view('/tasks/{id}', 'spa')->whereNumber('id')->middleware('auth');

// Admin user management
Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function () {
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::post('users/{user}/toggle', [\App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle');
});

// Task management
Route::get('my-tasks', [\App\Http\Controllers\TaskController::class, 'myTasks'])->name('tasks.my');
Route::resource('tasks', \App\Http\Controllers\TaskController::class);

// Comments and attachments
Route::post('tasks/{task}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('tasks.comments.store');
Route::post('tasks/{task}/attachments', [\App\Http\Controllers\AttachmentController::class, 'store'])->name('tasks.attachments.store');
Route::get('attachments/{attachment}/download', [\App\Http\Controllers\AttachmentController::class, 'download'])->name('attachments.download');

// Notifications
Route::post('notifications/mark-read', function() {
    if(Auth::check()) {
        \App\Models\Notification::where('user_id', Auth::id())->whereNull('read_at')->update(['read_at' => now()]);
    }
    return back();
})->name('notifications.read');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');
});
