<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id'          => ['sometimes', 'required', 'uuid', 'exists:accounts,id'],
            'transfer_account_id' => ['nullable', 'uuid', 'exists:accounts,id'],
            'category_id'         => ['nullable', 'uuid', 'exists:categories,id'],
            'type'                => ['sometimes', 'required', new Enum(TransactionType::class)],
            'amount'              => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'date'                => ['sometimes', 'required', 'date'],
            'note'                => ['nullable', 'string', 'max:1000'],
            'tags'                => ['nullable', 'array'],
            'tags.*'              => ['string', 'max:50'],
        ];
    }
}
