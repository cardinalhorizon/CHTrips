<?php

namespace Modules\CHTrips\Services;

use App\Repositories\AirlineRepository;
use App\Services\BidService;
use App\Services\FlightService;
use App\Services\ModuleService;
use Modules\CHTrips\Models\Enums\TripState;
use Modules\CHTrips\Models\FlightPirepTrip;
use Modules\CHTrips\Models\TripReport;

class CHTripsService
{
    public function __construct(
        public FlightService $flightService,
        public BidService $bidService,
        public AirlineRepository $airlineRepo,
        public ModuleService $moduleSvc
    ) {
    }
    public function createNewTrip($data)
    {
        //$data['flight_number'] = random_int(9001, 9999);
        //$data['minutes'] = 0;
        //$data['hours'] = 0;
        //$data['active'] = true;
        //$data['visible'] = false;
        //dd($data);
        // First, create the trip.
        $tr = new TripReport();
        $tr->name = $data['name'];
        $tr->state = TripState::UPCOMING;
        if ($data['description'] != "") {
            $tr->description = $data['description'];
        }

        $tr->save();

        $tr->users()->attach($data['user_id'], ['owner' => true]);
        // Now, create each flight based on the params
        $i = 0;
        foreach ($data['flights'] as $f) {
            $f['active'] = true;
            $flight = $this->flightService->createFlight($f);
            $flight->owner()->associate($tr);
            $flight->save();
            FlightPirepTrip::create([
                'trip_report_id' => $tr->id,
                'flight_id'      => $flight->id,
                'order'          => $i + 1
            ]);
            $i++;
        }
        return $tr;
    }
    public function completeTrip($fields)
    {

    }
}
