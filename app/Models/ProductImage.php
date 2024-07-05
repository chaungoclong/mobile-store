<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    public function product_detail(): BelongsTo
    {
        return $this->belongsTo(ProductDetail::class);
    }

    public function productDetail(): BelongsTo
    {
        return $this->belongsTo(ProductDetail::class, 'product_detail_id', 'id');
    }
}
