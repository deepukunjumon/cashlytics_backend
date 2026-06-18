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
}
