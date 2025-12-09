<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelOrderRequest;
use App\Http\Requests\PlaceOrderRequest;
use App\Http\Resources\OrderbookResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request): JsonResponse
    {
        $symbol = $request->get('symbol');
        $orderbookData = Order::getOrderbookData($symbol);

        $userSymbols = $request->user()
            ->assets()
            ->pluck('symbol')
            ->unique()
            ->values();

        return response()->json([
            'symbol' => $symbol,
            'buy_orders' => OrderbookResource::collection($orderbookData['buy_orders']),
            'sell_orders' => OrderbookResource::collection($orderbookData['sell_orders']),
            'user_symbols' => $userSymbols,
        ]);
    }

    public function userOrders(Request $request): JsonResponse
    {
        $orders = Order::forUser($request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(OrderResource::collection($orders));
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
                'order' => new OrderResource($order),
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
