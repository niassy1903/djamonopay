<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgentNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->unreadNotifications()
            ->latest()
            ->limit(20)
            ->get();

        $items = $notifications->map(fn ($notification): array => [
            'id' => $notification->id,
            'data' => $notification->data,
            'created_at' => $notification->created_at->toIso8601String(),
        ]);

        $notifications->each->markAsRead();

        return response()->json(['notifications' => $items]);
    }
}
