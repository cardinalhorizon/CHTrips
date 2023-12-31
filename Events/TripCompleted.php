<?php

namespace Modules\CHTrips\Events;

use App\Contracts\Event;
use Illuminate\Queue\SerializesModels;
use Modules\CHTrips\Models\TripReport;

/**
 * Class TripCompleted
 * @package Modules\CHTrips\Events
 */
class TripCompleted extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public TripReport $tripReport)
    {
        //
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
