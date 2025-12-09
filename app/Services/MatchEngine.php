<?php

namespace App\Services;

use App\Events\OrderMatched;
use App\Models\Asset;
use App\Models\Order;
use App\Models\Trade;
use Illuminate\Support\Facades\DB;

class MatchEngine
{
    public function process(Order $newOrder): void
    {
        $matchingOrders = $this->getMatchingOrders($newOrder);

        foreach ($matchingOrders as $existingOrder) {
            if (bccomp($newOrder->getRemainingAmount(), '0', 8) <= 0) {
                break;
            }

            $this->executeMatch($newOrder, $existingOrder);
        }
    }

    protected function getMatchingOrders(Order $newOrder): \Illuminate\Database\Eloquent\Collection
    {
        $query = Order::query()
            ->where('symbol', $newOrder->symbol)
            ->where('status', Order::STATUS_OPEN)
            ->where('user_id', '!=', $newOrder->user_id);

        if ($newOrder->side === 'buy') {
            $query->where('side', 'sell')
                ->where('price', '<=', $newOrder->price)
                ->orderBy('price', 'asc');
        } else {
            $query->where('side', 'buy')
                ->where('price', '>=', $newOrder->price)
                ->orderBy('price', 'desc');
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    protected function executeMatch(Order $buyOrder, Order $sellOrder): void
    {
        if ($buyOrder->side === 'sell') {
            [$buyOrder, $sellOrder] = [$sellOrder, $buyOrder];
        }

        DB::transaction(function () use ($buyOrder, $sellOrder) {
            $buyOrder = Order::where('id', $buyOrder->id)->lockForUpdate()->first();
            $sellOrder = Order::where('id', $sellOrder->id)->lockForUpdate()->first();

            $buyerAssetBase = Asset::where('user_id', $buyOrder->user_id)
                ->where('symbol', $buyOrder->symbol)
                ->lockForUpdate()
                ->first();

            $sellerAssetBase = Asset::where('user_id', $sellOrder->user_id)
                ->where('symbol', $sellOrder->symbol)
                ->lockForUpdate()
                ->first();

            $buyUser = $buyOrder->user()->lockForUpdate()->first();
            $sellUser = $sellOrder->user()->lockForUpdate()->first();

            $fillAmount = min(
                $buyOrder->getRemainingAmount(),
                $sellOrder->getRemainingAmount()
            );

            if (bccomp($fillAmount, '0', 8) <= 0) {
                return;
            }

            $tradePrice = $sellOrder->price;
            $commission = bcmul($fillAmount, '0.015', 8);
            $netAmount = bcsub($fillAmount, $commission, 8);

            if (! $buyerAssetBase) {
                $buyerAssetBase = Asset::create([
                    'user_id' => $buyOrder->user_id,
                    'symbol' => $buyOrder->symbol,
                    'amount' => '0',
                    'locked_amount' => '0',
                ]);
            }

            $buyerAssetBase->increment('amount', $netAmount);

            $sellerAssetBase->decrement('locked_amount', $fillAmount);
            $sellerAssetBase->decrement('amount', $fillAmount);

            $totalCost = bcmul($tradePrice, $fillAmount, 8);
            $sellUser->increment('balance', $totalCost);

            $buyOrder->increment('filled_amount', $fillAmount);
            $sellOrder->increment('filled_amount', $fillAmount);

            if (bccomp($buyOrder->getRemainingAmount(), '0', 8) <= 0) {
                $buyOrder->update(['status' => Order::STATUS_FILLED]);
            }

            if (bccomp($sellOrder->getRemainingAmount(), '0', 8) <= 0) {
                $sellOrder->update(['status' => Order::STATUS_FILLED]);
            }

            $trade = Trade::create([
                'buyer_order_id' => $buyOrder->id,
                'seller_order_id' => $sellOrder->id,
                'symbol' => $buyOrder->symbol,
                'price' => $tradePrice,
                'amount' => $fillAmount,
                'commission' => $commission,
            ]);

            event(new OrderMatched($trade, $buyOrder, $sellOrder));
        });
    }
}
