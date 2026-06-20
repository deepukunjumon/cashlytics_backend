<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'account_id',
        'transfer_account_id',
        'category_id',
        'type',
        'amount',
        'note',
        'date',
        'time',
        'tags',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'type'   => TransactionType::class,
            'amount' => 'decimal:2',
            'date'   => 'date',
            'tags'   => 'array',
        ];
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the account for the transaction.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the transfer destination account.
     */
    public function transferAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'transfer_account_id');
    }

    /**
     * Get the category for the transaction.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeOfType($query, TransactionType $type): void
    {
        $query->where('type', $type);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeForCategory($query, string $categoryId): void
    {
        $query->where('category_id', $categoryId);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeInDateRange($query, ?string $startDate, ?string $endDate): void
    {
        $query->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
              ->when($endDate,   fn($q) => $q->whereDate('date', '<=', $endDate));
    }

    /**
     * Scope to filter by month.
     */
    public function scopeInMonth($query, string $month): void
    {
        $query->whereYear('date', substr($month, 0, 4))
              ->whereMonth('date', substr($month, 5, 2));
    }

    /**
     * Scope to filter by authenticated user.
     */
    public function scopeForUser($query, string $userId): void
    {
        $query->where('user_id', $userId);
    }
}