<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountTypeField extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'account_type_id',
        'field_key',
        'field_label',
        'field_type',
        'options',
        'is_required',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'options'     => 'array',
            'is_required' => 'boolean',
        ];
    }

    /**
     * Get the account type this field belongs to.
     */
    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountTypeMaster::class, 'account_type_id');
    }
}
