<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['sometimes', 'required', 'string', 'max:255'],
            'type'    => ['sometimes', 'required', new Enum(AccountType::class)],
            'balance' => ['sometimes', 'required', 'numeric', 'min:0'],
            'color'   => ['nullable', 'string', 'max:7'],
            'notes'   => ['nullable', 'string', 'max:1000'],
        ];
    }
}
