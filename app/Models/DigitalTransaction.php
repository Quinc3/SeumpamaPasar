<?php

namespace App\Models;

use App\Enums\DigitalServiceType;
use App\Enums\DigitalTransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'digital_product_id',
        'type',
        'customer_number',
        'customer_name',
        'amount',
        'reference',
        'token_code',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'type' => DigitalServiceType::class,
            'amount' => 'decimal:2',
            'status' => DigitalTransactionStatus::class,
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function digitalProduct(): BelongsTo
    {
        return $this->belongsTo(DigitalProduct::class);
    }
}