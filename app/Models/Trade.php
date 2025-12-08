<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    protected $fillable = [
        'buyer_order_id',
        'seller_order_id',
        'symbol',
        'price',
        'amount',
        'commission',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:8',
            'amount' => 'decimal:8',
            'commission' => 'decimal:8',
        ];
    }

    public function buyerOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'buyer_order_id');
    }

    public function sellerOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'seller_order_id');
    }
}
