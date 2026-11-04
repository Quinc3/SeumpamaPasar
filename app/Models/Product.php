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
        'rating',
        'sold_count',
        'price',
        'discount_price',
        'stock',
        'sales_channel',
        'min_purchase_qty',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'rating' => 'decimal:2',
            'stock' => 'integer',
            'sold_count' => 'integer',
            'min_purchase_qty' => 'integer',
            'is_featured' => 'boolean',
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

    public function hasDiscount(): bool
    {
        return $this->discount_price !== null && (float) $this->discount_price < (float) $this->price;
    }

    public function discountPercent(): int
    {
        if (! $this->hasDiscount()) {
            return 0;
        }
        return (int) round((1 - (float) $this->discount_price / (float) $this->price) * 100);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }
}