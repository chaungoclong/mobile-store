<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producer extends Model
{
    protected $guarded = ['id'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getLogoUrlLinkAttribute(): string
    {
        return asset('storage/' . $this->getAttribute('logo') ?? '');
    }
}
