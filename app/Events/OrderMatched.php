<?php

namespace App\Events;

use App\Models\Order;
use App\Models\Trade;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderMatched implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Trade $trade,
        public Order $buyOrder,
        public Order $sellOrder
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->buyOrder->user_id),
            new PrivateChannel('user.'.$this->sellOrder->user_id),
            new Channel('symbol.'.$this->trade->symbol),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'trade' => [
                'id' => $this->trade->id,
                'symbol' => $this->trade->symbol,
                'price' => $this->trade->price,
                'amount' => $this->trade->amount,
                'created_at' => $this->trade->created_at,
            ],
            'buy_order' => [
                'id' => $this->buyOrder->id,
                'status' => $this->buyOrder->status,
                'filled_amount' => $this->buyOrder->filled_amount,
            ],
            'sell_order' => [
                'id' => $this->sellOrder->id,
                'status' => $this->sellOrder->status,
                'filled_amount' => $this->sellOrder->filled_amount,
            ],
        ];
    }
}
