<?php

namespace App\Http\Controllers;

use App\Enums\ApiResponseMessage;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $year  = $request->query('year', now()->year);
        $month = $request->query('month', now()->month);

        $budgets = Budget::forUser($request->user()->id)
            ->with('category')
            ->where('year', $year)
            ->where(function ($q) use ($month) {
                $q->whereNull('month')->orWhere('month', $month);
            })
            ->get()
            ->map(function ($budget) use ($request, $year, $month) {
                $spent = Transaction::forUser($request->user()->id)
                    ->where('category_id', $budget->category_id)
                    ->where('type', 'expense')
                    ->inMonth("{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT))
                    ->sum('amount');

                $budget->spent     = (float) $spent;
                $budget->remaining = max(0, (float) $budget->amount - (float) $spent);
                $budget->percent   = $budget->amount > 0
                    ? min(100, round(($spent / $budget->amount) * 100))
                    : 0;

                return $budget;
            });

        return $this->successResponse($budgets);
    }

    public function store(StoreBudgetRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $exists = Budget::forUser($request->user()->id)
            ->where('category_id', $data['category_id'])
            ->where('period', $data['period'])
            ->where('year', $data['year'])
            ->where('month', $data['month'] ?? null)
            ->exists();

        if ($exists) {
            return $this->errorResponse(ApiResponseMessage::BudgetExists->value, 422);
        }

        $budget = Budget::create($data);
        $budget->load('category');

        return $this->successResponse($budget, ApiResponseMessage::CreateSuccess->value, 201);
    }

    public function update(UpdateBudgetRequest $request, string $id): JsonResponse
    {
        $budget = Budget::forUser($request->user()->id)->find($id);

        if (! $budget) {
            return $this->errorResponse(ApiResponseMessage::BudgetNotFound->value, 404);
        }

        $budget->update($request->validated());

        return $this->successResponse($budget, ApiResponseMessage::UpdateSuccess->value);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $budget = Budget::forUser($request->user()->id)->find($id);

        if (! $budget) {
            return $this->errorResponse(ApiResponseMessage::BudgetNotFound->value, 404);
        }

        $budget->delete();

        return $this->successResponse(message: ApiResponseMessage::DeleteSuccess->value);
    }
}
