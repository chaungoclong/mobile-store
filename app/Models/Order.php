<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

class Order extends Model
{
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function payment_method()
    {
        return $this->belongsTo('App\Models\PaymentMethod');
    }

    public function order_details()
    {
        return $this->hasMany('App\Models\OrderDetail');
    }

    protected $guarded = ['id'];

    protected $status = [
        '1' => [
            'class' => 'default',
            'name' => 'Đang Xử Lý'
        ],

        '2' => [
            'class' => 'info',
            'name' => 'Đang Vận Chuyển'
        ],

        '3' => [
            'class' => 'success',
            'name' => 'Đã Giao Hàng'
        ],

        '-1' => [
            'class' => 'danger',
            'name' => 'Hủy'
        ],

    ];

    public function getStatus()
    {
        return Arr::get($this->status, "[N\A]");
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'id');
    }

    public function getCountProductsAttribute(): int
    {
        return $this->orderDetails()->sum('quantity');
    }
}
