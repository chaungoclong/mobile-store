<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductDetail extends Model
{
    protected $appends = [
        'discount_percent',
        'product_image_urls',
        'product_name'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function product_images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_detail_id', 'id');
    }

    public function order_details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'product_detail_id', 'id');
    }

    public function getDiscountPercentAttribute(): int
    {
        if ($this->hasPromotion() && $this->promotion_price < $this->sale_price) {
            return (int)round((($this->sale_price - $this->promotion_price) / $this->sale_price * 100));
        }

        return 0;
    }

    public function hasPromotion(): bool
    {
        if ($this->promotion_price !== null && $this->promotion_start_date !== null) {
            if ($this->promotion_end_date !== null) {
                return $this->promotion_start_date <= date('Y-m-d') && $this->promotion_end_date >= date('Y-m-d');
            }

            return $this->promotion_start_date <= date('Y-m-d');
        }

        return false;
    }

    public function getProductImageUrlsAttribute(): array
    {
        return $this->product_images()
            ->get()
            ->map(function (ProductImage $image) {
                return asset('storage/images/products/' . $image->getAttribute('image_name'));
            })
            ->toArray();
    }

    public function getProductNameAttribute(): string
    {
        return $this->product()->first()?->name ?? '';
    }
}
