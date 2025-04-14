<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "name",
        "image",
        "price",
        "stock",
    ];

    public function transaction(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
