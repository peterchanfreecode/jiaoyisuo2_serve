<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CandyEvent' => [
            'App\Listeners\CandyListener',
        ],
        'App\Events\RechargeEvent' => [
            'App\Listeners\RechargeListener',
        ],
        'App\Events\RealNameEvent' => [
            'App\Listeners\RealNameListener',
        ],
        'App\Events\RebateEvent' => [
            'App\Listeners\RebateListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
