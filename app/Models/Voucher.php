<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['code', 'discount_type', 'discount_value', 'expiry_date', 'usage_limit', 'used_count'];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'expiry_date' => 'datetime',
    ];

    public function isExpired(\DateTimeInterface $now): bool
    {
        return $this->expiry_date->lt($now);
    }

    public function hasRemainingUsage(): bool
    {
        return $this->used_count < $this->usage_limit;
    }
}
