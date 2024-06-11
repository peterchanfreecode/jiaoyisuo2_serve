<?php

namespace App\Listeners;

use App\Events\CandyEvent;
use App\Models\Users;

class CandyListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param LeverSubmitOrder $event
     * @return void
     */
    public function handle(CandyEvent $event)
    {
        $user_id = $event->user_id;
        $amount = $event->amount;
        $user_info = Users::find($user_id);
        if (!$user_info) {
            return false;
        }
        if ($amount < 0) {
            return false;
        }
        $user_info->candy_number = bcadd($user_info->candy_number, $amount, 4);
        $user_info->save();
    }
}
