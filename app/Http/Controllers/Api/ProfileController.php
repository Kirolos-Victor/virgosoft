<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use \App\Traits\FormatsCrypto;

    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('assets');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'balance' => $this->formatPrice($user->balance),
            ],
            'assets' => $user->assets->map(fn ($asset) => [
                'symbol' => $asset->symbol,
                'amount' => $this->formatAmount($asset->amount),
                'locked_amount' => $this->formatAmount($asset->locked_amount),
                'available' => $this->formatAmount(bcsub($asset->amount, $asset->locked_amount, 8)),
            ]),
        ]);
    }
}
