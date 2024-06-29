<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $appends = ['total_sold'];
    protected $guarded = ['id'];

    public function advertises()
    {
        return $this->hasMany('App\Models\Advertise');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function product_votes()
    {
        return $this->hasMany('App\Models\ProductVote');
    }

    public function promotions()
    {
        return $this->hasMany('App\Models\Promotion');
    }

    public function product_details()
    {
        return $this->hasMany('App\Models\ProductDetail');
    }

    public function producer()
    {
        return $this->belongsTo('App\Models\Producer');
    }

    public function product_detail()
    {
        return $this->hasOne('App\Models\ProductDetail', 'product_id', 'id');
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
}
