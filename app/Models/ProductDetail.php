<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    protected $appends = ['discount_percent'];

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function product_images()
    {
        return $this->hasMany('App\Models\ProductImage');
    }

    public function order_details()
    {
        return $this->hasMany('App\Models\OrderDetail');
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
}
