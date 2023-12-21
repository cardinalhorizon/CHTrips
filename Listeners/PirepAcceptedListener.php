<?php

namespace Modules\CHTrips\Listeners;

use App\Contracts\Listener;
use App\Events\PirepAccepted;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use App\Models\Flight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\CHTrips\Events\TripCompleted;
use Modules\CHTrips\Models\Enums\TripState;
use Modules\CHTrips\Models\FlightPirepTrip;
use Modules\CHTrips\Models\TripReport;
use MongoDB\Driver\BulkWrite;

/**
 * Class PirepAcceptedListener
 * @package Modules\CHTrips\Listeners
 */
class PirepAcceptedListener extends Listener
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
    public function handle(PirepAccepted $event)
    {
        $user = $event->pirep->user_id;
        $flight = $event->pirep->flight_id;
        $pirep_id = $event->pirep->id;

        $active_trip = TripReport::where(['owner_id' => $user])->whereIn('state', [TripState::UPCOMING, TripState::IN_PROGRESS])->whereHas('flights', function (Builder $query) use ($flight) {
            $query->where('flight_id', $flight);
        })->with('fpts')->first();
        if ($active_trip === null) {
            return;
        }
        FlightPirepTrip::where(['trip_report_id' => $active_trip->id, 'flight_id' => $flight])->update(['completed' => true]);

        // Check for Trip Completion
        $completed = true;
        foreach ($active_trip->fpts as $fpt) {
            if ($fpt->completed) {
                continue;
            }
            $completed = false;
            break;
        }
        if ($completed) {
            // Trip has been completed.
            $active_trip->state = TripState::COMPLETED;
            $active_trip->save();
            // Delete the flights that are user created
            Flight::whereHasMorph('owner', [TripReport::class], function (Builder $builder) use ($active_trip) {
                $builder->where('owner_id', $active_trip->id);
            })->delete();

            // Trigger the Trip Completion Event
            event(new TripCompleted($active_trip));
        }
    }
}
