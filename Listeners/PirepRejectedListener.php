<?php

namespace Modules\CHTrips\Listeners;

use App\Contracts\Listener;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class PirepRejectedListener
 * @package Modules\CHTrips\Listeners
 */
class PirepRejectedListener extends Listener
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
    }
}
