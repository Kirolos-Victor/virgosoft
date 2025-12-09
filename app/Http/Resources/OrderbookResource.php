<?php

namespace App\Http\Resources;

use App\Traits\FormatsCrypto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderbookResource extends JsonResource
{
    use FormatsCrypto;

    public function toArray(Request $request): array
    {
        return [
            'price' => $this->formatPrice($this->price),
            'total_amount' => $this->formatAmount($this->total_amount),
        ];
    }
}
