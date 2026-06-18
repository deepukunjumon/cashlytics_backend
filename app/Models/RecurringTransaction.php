<?php

namespace App\Models;

use App\Enums\RecurringFrequency;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecurringTransaction extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'type',
        'amount',
        'note',
        'frequency',
        'starts_at',
        'ends_at',
        'next_due_at',
        'last_generated_at',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'type'               => TransactionType::class,
            'frequency'          => RecurringFrequency::class,
            'amount'             => 'decimal:2',
            'starts_at'          => 'date',
            'ends_at'            => 'date',
            'next_due_at'        => 'date',
            'last_generated_at'  => 'datetime',
            'is_active'          => 'boolean',
        ];
    }

    /**
     * Get the user that owns this recurring transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the account for this recurring transaction.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the category for this recurring transaction.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope to filter by authenticated user.
     */
    public function scopeForUser($query, string $userId): void
    {
        $query->where('user_id', $userId);
    }
}
