<?php

namespace App\Http\Controllers;

use App\Enums\ApiResponseMessage;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = Category::where(function ($q) use ($request) {
            $q->where('user_id', $request->user()->id)
              ->orWhereNull('user_id');
        })
        ->where('is_active', true)
        ->orderByRaw('is_system DESC')
        ->orderBy('name')
        ->get();

        return $this->successResponse($categories);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create(array_merge(
            $request->validated(),
            ['user_id' => $request->user()->id, 'is_system' => false, 'is_active' => true]
        ));

        return $this->successResponse($category, ApiResponseMessage::CreateSuccess->value, 201);
    }

    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        $category = Category::where('user_id', $request->user()->id)->find($id);

        if (! $category) {
            return $this->errorResponse(ApiResponseMessage::CategoryNotFound->value, 404);
        }

        $category->update($request->validated());

        return $this->successResponse($category, ApiResponseMessage::UpdateSuccess->value);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $category = Category::where('user_id', $request->user()->id)->find($id);

        if (! $category) {
            return $this->errorResponse(ApiResponseMessage::CategoryNotFound->value, 404);
        }

        if ($category->transactions()->exists()) {
            return $this->errorResponse(ApiResponseMessage::CategoryHasTransactions->value, 422);
        }

        $category->delete();

        return $this->successResponse(message: ApiResponseMessage::DeleteSuccess->value);
    }
}
