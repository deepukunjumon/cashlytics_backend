<?php

namespace App\Http\Controllers\Superadmin;

use App\Enums\ApiResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuperadminCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->successResponse(Category::whereNull('user_id')->orderBy('type')->orderBy('name')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'type'  => ['required', 'in:income,expense'],
            'color' => ['nullable', 'string', 'max:7'],
            'icon'  => ['nullable', 'string', 'max:50'],
        ]);

        $category = Category::create(array_merge($data, [
            'user_id'   => null,
            'is_system' => true,
            'is_active' => true,
        ]));

        return $this->successResponse($category, ApiResponseMessage::CreateSuccess->value, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $category = Category::whereNull('user_id')->find($id);

        if (! $category) {
            return $this->errorResponse(ApiResponseMessage::CategoryNotFound->value, 404);
        }

        $data = $request->validate([
            'name'      => ['sometimes', 'required', 'string', 'max:255'],
            'color'     => ['nullable', 'string', 'max:7'],
            'icon'      => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $category->update($data);

        return $this->successResponse($category, ApiResponseMessage::UpdateSuccess->value);
    }

    public function destroy(string $id): JsonResponse
    {
        $category = Category::whereNull('user_id')->find($id);

        if (! $category) {
            return $this->errorResponse(ApiResponseMessage::CategoryNotFound->value, 404);
        }

        $category->delete();

        return $this->successResponse(message: ApiResponseMessage::DeleteSuccess->value);
    }
}
