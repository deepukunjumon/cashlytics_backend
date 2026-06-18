<?php

namespace App\Http\Requests;

use App\Enums\BudgetPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'uuid', 'exists:categories,id'],
            'period'      => ['required', new Enum(BudgetPeriod::class)],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'year'        => ['required', 'integer', 'min:2000', 'max:2100'],
            'month'       => ['nullable', 'integer', 'min:1', 'max:12'],
        ];
    }
}
