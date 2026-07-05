<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    // Main user-facing lifecycle statuses (must not disappear from the UI)
    public const STATUS_PACKING = 'Sedang Dikemas';
    public const STATUS_WAITING_DRIVER = 'Menunggu Pengirim';
    public const STATUS_SHIPPING = 'Sedang Dikirim';
    public const STATUS_DONE = 'Pesanan Selesai';
    public const STATUS_RETURNED = 'Dikembalikan';

    protected $fillable = [
        'buyer_id', 'store_id', 'driver_id', 'delivery_method',
        'subtotal', 'discount', 'delivery_fee', 'ppn', 'total',
        'voucher_id', 'promo_id', 'status', 'sla_due_at', 'overdue_processed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'ppn' => 'decimal:2',
        'total' => 'decimal:2',
        'sla_due_at' => 'datetime',
        'overdue_processed_at' => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('changed_at');
    }

    public function deliveryJob(): HasOne
    {
        return $this->hasOne(DeliveryJob::class);
    }
}
