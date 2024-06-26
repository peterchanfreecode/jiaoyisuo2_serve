<?php

namespace App\Events;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
class CandyEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public  $user_id;
    public  $amount;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id,$amount)
    {
        $this->user_id = $user_id;
        $this->amount = $amount;
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
