<?php

namespace App\Http\Controllers;

use App\Enums\ApiResponseMessage;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::forUser($request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return $this->successResponse($notifications);
    }

    public function markRead(Request $request): JsonResponse
    {
        Notification::forUser($request->user()->id)
            ->unread()
            ->update(['read_at' => now()]);

        return $this->successResponse(message: ApiResponseMessage::NotificationMarkRead->value);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = Notification::forUser($request->user()->id)->find($id);

        if (! $notification) {
            return $this->errorResponse(ApiResponseMessage::NotFound->value, 404);
        }

        $notification->delete();

        return $this->successResponse(message: ApiResponseMessage::DeleteSuccess->value);
    }

    // ──────────────────────────────────────────────────────
    //  Native Laravel Notifications (notifiable relationship) — used by
    //  notify()-driven notifications like LargeExpenseAdded, which live in the
    //  same table as the legacy rows above but are queried via notifiable_id
    //  instead of user_id.
    // ──────────────────────────────────────────────────────

    public function realtimeIndex(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()->paginate(20);

        return $this->successResponse($notifications);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return $this->successResponse(['count' => $request->user()->unreadNotifications()->count()]);
    }

    public function markOneRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->find($id);

        if (! $notification) {
            return $this->errorResponse(ApiResponseMessage::NotFound->value, 404);
        }

        $notification->markAsRead();

        return $this->successResponse($notification, ApiResponseMessage::NotificationMarkRead->value);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->successResponse(message: ApiResponseMessage::NotificationMarkRead->value);
    }

    public function clearAll(Request $request): JsonResponse
    {
        $request->user()->notifications()->delete();

        return $this->successResponse(message: ApiResponseMessage::DeleteSuccess->value);
    }
}
