<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountTypeMaster extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'account_types';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
        'is_active',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all dynamic fields for this account type.
     */
    public function fields(): HasMany
    {
        return $this->hasMany(AccountTypeField::class, 'account_type_id')->orderBy('sort_order');
    }

    /**
     * Get all accounts of this type.
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'account_type_id');
    }
}
