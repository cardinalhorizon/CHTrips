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

        //$active_trip = TripReport::whereHas('users', function ($q) use ($user) { $q->where('user_id', $user);})->whereIn('state', [TripState::UPCOMING, TripState::IN_PROGRESS])->whereHas('flights', function (Builder $query) use ($flight) {
        //    $query->where('flight_id', $flight);
        //})->with('fpts')->first();
        //dd($active_trip);
        //if ($active_trip === null) {
        //    return;
        //}

        //FlightPirepTrip::where(['trip_report_id' => $active_trip->id, 'flight_id' => $flight])->update(['completed' => true]);

        // Check for Trip Completion

        $active_trip = FlightPirepTrip::with('trip_report')->where('pirep_id', $pirep_id)->first()->trip_report()->first();
        if ($active_trip === null) {
            return;
        }
        $active_trip->load('fpts');
        //dd($active_trip);
        $completed = true;
        foreach ($active_trip->fpts as $fpt) {
            if ($fpt->pirep->state = PirepState::ACCEPTED) {
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
