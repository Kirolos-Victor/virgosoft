<?php

namespace App\Http\Resources;

use App\Traits\FormatsCrypto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use FormatsCrypto;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'balance' => $this->formatPrice($this->balance),
        ];
    }
}
