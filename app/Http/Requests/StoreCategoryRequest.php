<?php

namespace App\Http\Requests;

use App\Enums\CategoryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'type'      => ['required', new Enum(CategoryType::class)],
            'color'     => ['nullable', 'string', 'max:7'],
            'icon'      => ['nullable', 'string', 'max:50'],
            'parent_id' => ['nullable', 'uuid', 'exists:categories,id'],
        ];
    }
}
