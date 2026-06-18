<?php

namespace App\Http\Controllers;

use App\Enums\ApiResponseMessage;
use App\Http\Requests\StoreRecurringTransactionRequest;
use App\Models\RecurringTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecurringTransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = RecurringTransaction::forUser($request->user()->id)
            ->with(['account', 'category'])
            ->orderBy('next_due_at')
            ->get();

        return $this->successResponse($items);
    }

    public function store(StoreRecurringTransactionRequest $request): JsonResponse
    {
        $data     = $request->validated();
        $startsAt = $data['starts_at'];

        $item = RecurringTransaction::create(array_merge($data, [
            'user_id'     => $request->user()->id,
            'next_due_at' => $startsAt,
        ]));

        $item->load(['account', 'category']);

        return $this->successResponse($item, ApiResponseMessage::CreateSuccess->value, 201);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $item = RecurringTransaction::forUser($request->user()->id)->find($id);

        if (! $item) {
            return $this->errorResponse(ApiResponseMessage::NotFound->value, 404);
        }

        $item->delete();

        return $this->successResponse(message: ApiResponseMessage::DeleteSuccess->value);
    }
}
