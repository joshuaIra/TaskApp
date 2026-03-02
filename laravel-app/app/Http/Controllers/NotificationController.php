<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($notifications);
    }

    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        if ((int) $notification->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        if ($notification->read_at === null) {
            $notification->update([
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'notification' => $notification->fresh(),
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        Notification::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
