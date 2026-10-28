<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'checkout_id',
        'seller_profile_id',
        'buyer_id',
        'subtotal',
        'shipping_recipient_name',
        'shipping_phone',
        'shipping_line1',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'status' => OrderStatus::class,
            'stock_deducted_at' => 'datetime',
            'stock_restored_at' => 'datetime',
            'refunded_amount' => 'decimal:2',
            'refunded_at' => 'datetime',
            'escrow_amount' => 'decimal:2',
            'escrowed_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function checkout(): BelongsTo
    {
        return $this->belongsTo(Checkout::class);
    }

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isEscrowHeld(): bool
    {
        return $this->escrowed_at !== null && $this->released_at === null;
    }

    public function scopeEscrowHeld($query)
    {
        return $query->whereNotNull('escrowed_at')->whereNull('released_at');
    }
}