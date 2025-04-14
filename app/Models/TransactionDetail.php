<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "transaction_id", "product_id", "quantity", "subtotal"
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
