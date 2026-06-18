<?php

namespace App\Http\Controllers\Superadmin;

use App\Enums\ApiResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuperadminCurrencyController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->successResponse(Currency::orderBy('code')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code'      => ['required', 'string', 'max:10', 'unique:currencies,code'],
            'symbol'    => ['required', 'string', 'max:10'],
            'name'      => ['required', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        $currency = Currency::create($data);

        return $this->successResponse($currency, ApiResponseMessage::CreateSuccess->value, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $currency = Currency::find($id);

        if (! $currency) {
            return $this->errorResponse(ApiResponseMessage::NotFound->value, 404);
        }

        $data = $request->validate([
            'symbol'    => ['sometimes', 'required', 'string', 'max:10'],
            'name'      => ['sometimes', 'required', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        $currency->update($data);

        return $this->successResponse($currency, ApiResponseMessage::UpdateSuccess->value);
    }
}
