<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductDetail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function product_detail()
    {
        return $this->belongsTo('App\Models\ProductDetail');
    }

    public function productDetail(): BelongsTo
    {
        return $this->belongsTo(ProductDetail::class);
    }
}
