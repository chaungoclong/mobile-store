<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product_detail()
    {
        return $this->belongsTo('App\Models\ProductDetail');
    }

    public function productDetail(): BelongsTo
    {
        return $this->belongsTo(ProductDetail::class, 'product_detail_id', 'id');
    }
}
