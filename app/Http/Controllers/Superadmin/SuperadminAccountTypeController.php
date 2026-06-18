<?php

namespace App\Http\Controllers\Superadmin;

use App\Enums\ApiResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\AccountTypeMaster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuperadminAccountTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->successResponse(AccountTypeMaster::with('fields')->orderBy('sort_order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['required', 'string', 'max:50', 'unique:account_types,slug'],
            'icon'        => ['nullable', 'string', 'max:50'],
            'color'       => ['nullable', 'string', 'max:7'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['integer', 'min:0'],
        ]);

        $type = AccountTypeMaster::create(array_merge($data, ['is_active' => true]));

        return $this->successResponse($type, ApiResponseMessage::CreateSuccess->value, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $type = AccountTypeMaster::find($id);

        if (! $type) {
            return $this->errorResponse(ApiResponseMessage::NotFound->value, 404);
        }

        $data = $request->validate([
            'name'        => ['sometimes', 'required', 'string', 'max:100'],
            'icon'        => ['nullable', 'string', 'max:50'],
            'color'       => ['nullable', 'string', 'max:7'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active'   => ['boolean'],
            'sort_order'  => ['integer', 'min:0'],
        ]);

        $type->update($data);

        return $this->successResponse($type, ApiResponseMessage::UpdateSuccess->value);
    }
}
