<?php

namespace App\Models;

use App\Enums\SellerStatus;
use App\Enums\SellerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seller_type',
        'store_name',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'seller_type' => SellerType::class,
            'status' => SellerStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}