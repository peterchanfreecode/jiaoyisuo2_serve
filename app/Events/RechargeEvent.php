<?php

namespace App\Events;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
class RechargeEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId; //用户id
    public $type; //1 绑定充值事件 2 绑定新币购买事件
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id,$type)
    {
        $this->userId = $user_id;
        $this->type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
