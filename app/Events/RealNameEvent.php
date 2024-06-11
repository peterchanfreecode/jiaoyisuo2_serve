<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Users;
use App\Models\UserReal;
use App\Models\UserRealHigh;
class RealNameEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $user;
    protected $userReal;
    protected $userRealHigh;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Users $user, UserReal $user_real,UserRealHigh $userRealHigh)
    {
        $this->user = $user;
        $this->userReal = $user_real;
        $this->userRealHigh = $userRealHigh;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getUserReal()
    {
        return $this->userReal;
    }
    public function getUserRealHigh()
    {
        return $this->userRealHigh;
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
