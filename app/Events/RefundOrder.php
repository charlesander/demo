<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class RefundOrder
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Request
     */
    public Request $request;

    /**
     * @var Order
     */
    public Order $order;

    /**
     * @var string
     */
    public string $source;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, Order $order, string $source)
    {
        $this->request = $request;
        $this->order = $order;
        $this->source = $source;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
