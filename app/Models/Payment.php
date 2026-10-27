<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'checkout_id',
        'method',
        'reference',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function checkout(): BelongsTo
    {
        return $this->belongsTo(Checkout::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && now()->gt($this->expires_at);
    }
}