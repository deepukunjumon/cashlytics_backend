<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApiResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::select('id', 'name', 'email', 'mobile', 'is_admin', 'created_at')
            ->withCount(['transactions', 'categories'])
            ->orderByDesc('created_at')
            ->get();

        return $this->successResponse($users);
    }

    public function show(string $id): JsonResponse
    {
        $user = User::select('id', 'name', 'email', 'mobile', 'is_admin', 'created_at')
            ->withCount(['transactions', 'categories'])
            ->find($id);

        if (! $user) {
            return $this->errorResponse(ApiResponseMessage::NotFound->value, 404);
        }

        $income  = $user->transactions()->where('type', 'income')->sum('amount');
        $expense = $user->transactions()->where('type', 'expense')->sum('amount');

        return $this->successResponse([
            'user'    => $user,
            'summary' => [
                'total_income'  => number_format((float) $income, 2, '.', ''),
                'total_expense' => number_format((float) $expense, 2, '.', ''),
                'net_balance'   => number_format((float) ($income - $expense), 2, '.', ''),
            ],
        ]);
    }

    public function destroy(string $id, \Illuminate\Http\Request $request): JsonResponse
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
}
