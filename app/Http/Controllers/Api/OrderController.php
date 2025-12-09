<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelOrderRequest;
use App\Http\Requests\PlaceOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use \App\Traits\FormatsCrypto;

    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request): JsonResponse
    {
        $symbol = $request->get('symbol');

        $query = Order::query()
            ->where('status', Order::STATUS_OPEN)
            ->orderBy('price');

        if ($symbol) {
            $query->where('symbol', $symbol);
        }

        $buyOrders = (clone $query)
            ->where('side', 'buy')
            ->orderBy('price', 'desc')
            ->selectRaw('price, SUM(amount - filled_amount) as total_amount')
            ->groupBy('price')
            ->limit(20)
            ->get()
            ->map(fn ($order) => [
                'price' => $this->formatPrice($order->price),
                'total_amount' => $this->formatAmount($order->total_amount),
            ]);

        $sellOrders = (clone $query)
            ->where('side', 'sell')
            ->orderBy('price', 'asc')
            ->selectRaw('price, SUM(amount - filled_amount) as total_amount')
            ->groupBy('price')
            ->limit(20)
            ->get()
            ->map(fn ($order) => [
                'price' => $this->formatPrice($order->price),
                'total_amount' => $this->formatAmount($order->total_amount),
            ]);

        $userSymbols = $request->user()
            ->assets()
            ->pluck('symbol')
            ->unique()
            ->values();

        return response()->json([
            'symbol' => $symbol,
            'buy_orders' => $buyOrders,
            'sell_orders' => $sellOrders,
            'user_symbols' => $userSymbols,
        ]);
    }

    public function userOrders(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function store(PlaceOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->placeLimitOrder(
                $request->user(),
                $request->input('symbol'),
                $request->input('side'),
                $request->input('price'),
                $request->input('amount')
            );

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to place order',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(Order $order, CancelOrderRequest $request): JsonResponse
    {
        try {
            $this->orderService->cancelOrder($order);

            return response()->json([
                'message' => 'Order cancelled successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
