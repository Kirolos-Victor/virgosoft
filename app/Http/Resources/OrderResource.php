<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'symbol' => $this->symbol,
            'side' => $this->side,
            'price' => $this->price,
            'amount' => $this->amount,
            'filled_amount' => $this->filled_amount,
            'status' => $this->status,
            'formatted_price' => $this->formatted_price,
            'formatted_amount' => $this->formatted_amount,
            'formatted_filled_amount' => $this->formatted_filled_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
