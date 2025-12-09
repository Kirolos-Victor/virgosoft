<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(protected MatchEngine $matchEngine) {}

    public function placeLimitOrder(User $user, string $symbol, string $side, string $price, string $amount): Order
    {
        return DB::transaction(function () use ($user, $symbol, $side, $price, $amount) {
            if ($side === 'buy') {
                $this->lockUserBalance($user, $price, $amount);
            } else {
                $this->lockUserAsset($user, $symbol, $amount);
            }

            $order = Order::create([
                'user_id' => $user->id,
                'symbol' => $symbol,
                'side' => $side,
                'price' => $price,
                'amount' => $amount,
                'filled_amount' => '0',
                'status' => Order::STATUS_OPEN,
            ]);

            $this->matchEngine->process($order);

            return $order->fresh();
        });
    }

    public function cancelOrder(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            if (! $order->isOpen()) {
                throw new \Exception('Only open orders can be cancelled');
            }

            $order->lockForUpdate()->first();

            $remainingAmount = $order->getRemainingAmount();

            if (bccomp($remainingAmount, '0', 8) > 0) {
                if ($order->side === 'buy') {
                    $this->unlockUserBalance($order->user, $order->price, $remainingAmount);
                } else {
                    $this->unlockUserAsset($order->user, $order->symbol, $remainingAmount);
                }
            }

            $order->update(['status' => Order::STATUS_CANCELLED]);

            return true;
        });
    }

    protected function lockUserBalance(User $user, string $price, string $amount): void
    {
        $user->lockForUpdate()->first();

        $totalCost = bcmul($price, $amount, 8);

        if (bccomp($user->balance, $totalCost, 8) < 0) {
            throw new \Exception('Insufficient balance');
        }

        $user->decrement('balance', $totalCost);
    }

    protected function unlockUserBalance(User $user, string $price, string $amount): void
    {
        $totalCost = bcmul($price, $amount, 8);
        $user->increment('balance', $totalCost);
    }

    protected function lockUserAsset(User $user, string $symbol, string $amount): void
    {
        $asset = Asset::where('user_id', $user->id)
            ->where('symbol', $symbol)
            ->lockForUpdate()
            ->first();

        if (! $asset) {
            throw new \Exception('Asset not found');
        }

        $availableAmount = bcsub($asset->amount, $asset->locked_amount, 8);

        if (bccomp($availableAmount, $amount, 8) < 0) {
            throw new \Exception('Insufficient asset amount');
        }

        $asset->increment('locked_amount', $amount);
    }

    protected function unlockUserAsset(User $user, string $symbol, string $amount): void
    {
        $asset = Asset::where('user_id', $user->id)
            ->where('symbol', $symbol)
            ->first();

        if ($asset) {
            $asset->decrement('locked_amount', $amount);
        }
    }
}
