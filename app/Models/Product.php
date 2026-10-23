<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Enums\SalesChannel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_profile_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'sales_channel',
        'min_purchase_qty',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'min_purchase_qty' => 'integer',
            'sales_channel' => SalesChannel::class,
            'status' => ProductStatus::class,
        ];
    }

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function wholesalePriceTiers(): HasMany
    {
        return $this->hasMany(WholesalePriceTier::class)->orderByDesc('min_quantity');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}