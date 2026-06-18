<?php

namespace App\Http\Requests;

use App\Enums\RecurringFrequency;
use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreRecurringTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id'  => ['required', 'uuid', 'exists:accounts,id'],
            'category_id' => ['nullable', 'uuid', 'exists:categories,id'],
            'type'        => ['required', new Enum(TransactionType::class)],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'note'        => ['nullable', 'string', 'max:1000'],
            'frequency'   => ['required', new Enum(RecurringFrequency::class)],
            'starts_at'   => ['required', 'date'],
            'ends_at'     => ['nullable', 'date', 'after:starts_at'],
        ];
    }
}
