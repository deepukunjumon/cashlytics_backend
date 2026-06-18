<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'account_type_id',
        'name',
        'type',
        'balance',
        'is_archived',
        'is_primary',
        'notes',
        'color',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'type'        => AccountType::class,
            'balance'     => 'float',
            'is_archived' => 'boolean',
            'is_primary'  => 'boolean',
        ];
    }

    /**
     * Get the user that owns the account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by authenticated user.
     */
    /**
     * Get the account type master record.
     */
    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountTypeMaster::class, 'account_type_id');
    }

    /**
     * Get all field values for the account.
     */
    public function fieldValues(): HasMany
    {
        return $this->hasMany(AccountFieldValue::class);
    }

    /**
     * Scope to filter by authenticated user.
     */
    public function scopeForUser($query, string $userId): void
    {
        $query->where('user_id', $userId);
    }
}
