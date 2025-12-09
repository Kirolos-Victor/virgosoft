<?php

namespace App\Models;

use App\Traits\FormatsCrypto;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use FormatsCrypto;

    public const STATUS_OPEN = 1;

    public const STATUS_FILLED = 2;

    public const STATUS_CANCELLED = 3;

    protected $fillable = [
        'user_id',
        'symbol',
        'side',
        'price',
        'amount',
        'filled_amount',
        'status',
    ];

    protected $appends = ['formatted_price', 'formatted_amount', 'formatted_filled_amount'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:8',
            'amount' => 'decimal:8',
            'filled_amount' => 'decimal:8',
        ];
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatPrice($this->price),
        );
    }

    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatAmount($this->amount),
        );
    }

    protected function formattedFilledAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatAmount($this->filled_amount),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function buyTrades(): HasMany
    {
        return $this->hasMany(Trade::class, 'buyer_order_id');
    }

    public function sellTrades(): HasMany
    {
        return $this->hasMany(Trade::class, 'seller_order_id');
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isFilled(): bool
    {
        return $this->status === self::STATUS_FILLED;
    }

    public function getRemainingAmount(): string
    {
        return bcsub($this->amount, $this->filled_amount, 8);
    }
}
