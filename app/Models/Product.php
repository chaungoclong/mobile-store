<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $appends = ['total_sold'];
    protected $guarded = ['id'];

    public function product_votes(): HasMany
    {
        return $this->hasMany(ProductVote::class);
    }

    public function product_details(): HasMany
    {
        return $this->hasMany(ProductDetail::class);
    }

    public function producer(): BelongsTo
    {
        return $this->belongsTo(Producer::class);
    }

    public function product_detail(): HasOne
    {
        return $this->hasOne(ProductDetail::class, 'product_id', 'id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ProductVote::class, 'product_id', 'id');
    }

    public function productDetails(): HasMany
    {
        return $this->hasMany(ProductDetail::class, 'product_id', 'id');
    }

    public function getTotalSoldAttribute(): int
    {
        return OrderDetail::query()
            ->whereHas('order', function ($query) {
                $query->where('status', OrderStatus::Done->value); // Filter orders with status Done
            })
            ->whereHas('productDetail.product', function ($query) {
                $query->where('id', $this->getKey()); // Filter by product_id
            })
            ->sum('quantity');
    }

    public function images(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProductImage::class,
            ProductDetail::class,
            'product_id',
            'product_detail_id',
            'id',
            'id'
        );
    }
}
