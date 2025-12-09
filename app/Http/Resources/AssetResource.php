<?php

namespace App\Http\Resources;

use App\Traits\FormatsCrypto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    use FormatsCrypto;

    public function toArray(Request $request): array
    {
        return [
            'symbol' => $this->symbol,
            'amount' => $this->formatAmount($this->amount),
            'locked_amount' => $this->formatAmount($this->locked_amount),
            'available' => $this->formatAmount(bcsub($this->amount, $this->locked_amount, 8)),
        ];
    }
}
