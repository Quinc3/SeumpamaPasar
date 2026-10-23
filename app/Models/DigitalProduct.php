<?php

namespace App\Models;

use App\Enums\DigitalServiceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DigitalProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'code',
        'name',
        'provider',
        'nominal_value',
        'base_price',
        'metadata',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => DigitalServiceType::class,
            'nominal_value' => 'decimal:2',
            'base_price' => 'decimal:2',
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function digitalTransactions(): HasMany
    {
        return $this->hasMany(DigitalTransaction::class);
    }
}