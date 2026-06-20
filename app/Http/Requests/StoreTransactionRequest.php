<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id'          => ['required', 'uuid', 'exists:accounts,id'],
            'transfer_account_id' => ['nullable', 'uuid', 'exists:accounts,id', 'different:account_id'],
            'category_id'         => ['nullable', 'uuid', 'exists:categories,id'],
            'type'                => ['required', new Enum(TransactionType::class)],
            'amount'              => ['required', 'numeric', 'min:0.01'],
            'date'                => ['required', 'date'],
            'time'                => ['nullable', 'date_format:H:i'],
            'note'                => ['nullable', 'string', 'max:1000'],
            'tags'                => ['nullable', 'array'],
            'tags.*'              => ['string', 'max:50'],
        ];
    }
}
