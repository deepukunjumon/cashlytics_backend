<?php

namespace App\Http\Controllers\Superadmin;

use App\Enums\ApiResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuperadminUserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::withTrashed()
            ->withCount('accounts')
            ->latest()
            ->paginate(20);

        return $this->successResponse($users);
    }

    public function show(string $id): JsonResponse
    {
        $user = User::withTrashed()->withCount('accounts')->find($id);

        if (! $user) {
            return $this->errorResponse(ApiResponseMessage::NotFound->value, 404);
        }

        return $this->successResponse($user);
    }

    public function toggleStatus(Request $request, string $id): JsonResponse
    {
        $user = User::withTrashed()->find($id);

        if (! $user) {
            return $this->errorResponse(ApiResponseMessage::NotFound->value, 404);
        }

        if ($user->id === $request->user()->id) {
            return $this->errorResponse(ApiResponseMessage::CannotDeleteSelf->value, 422);
        }

        if ($user->trashed()) {
            $user->restore();
            $message = 'User activated successfully.';
        } else {
            $user->delete();
            $message = 'User deactivated successfully.';
        }

        return $this->successResponse($user->fresh(), $message);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return $this->errorResponse(ApiResponseMessage::NotFound->value, 404);
        }

        if ($user->id === $request->user()->id) {
            return $this->errorResponse(ApiResponseMessage::CannotDeleteSelf->value, 422);
        }

        $user->delete();

        return $this->successResponse(message: ApiResponseMessage::DeleteSuccess->value);
    }

    public function restore(string $id): JsonResponse
    {
        $user = User::onlyTrashed()->find($id);

        if (! $user) {
            return $this->errorResponse(ApiResponseMessage::NotFound->value, 404);
        }

        $user->restore();

        return $this->successResponse(message: ApiResponseMessage::UpdateSuccess->value);
    }
}
