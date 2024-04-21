<?php

namespace Modules\CHTrips\tests;

use App\Models\Flight;
use App\Models\Pirep;
use App\Services\BidService;
use App\Services\FlightService;
use App\Services\PirepService;
use Modules\CHTrips\Models\Enums\TripState;
use Modules\CHTrips\Models\FlightPirepTrip;
use Modules\CHTrips\Models\TripReport;
use Modules\CHTrips\Services\CHTripsService;
use Tests\TestCase;

class TripManagementTest extends TestCase
{
    protected CHTripsService $tripService;
    protected PirepService $pirepService;
    protected FlightService $flightService;
    protected BidService $bidsService;

    protected TripReport $testTrip;

    protected array $trip_case = [
        'name' => "Free Flight",
        'description' => "Flight Description",
        'user_id' => 1,
        'flights' => [
            [
                'airline_id' => 1,
                'flight_number' => '1521',
                'route_leg' => 1,
                'dpt_airport_id' => 'KLAX',
                'arr_airport_id' => 'KDEN',
                'minutes' => 0,
                'hours' => 0
            ],
            [
                'airline_id' => 1,
                'flight_number' => '1521',
                'route_leg' => 2,
                'dpt_airport_id' => 'KDEN',
                'arr_airport_id' => 'KSFO',
                'minutes' => 0,
                'hours' => 0
            ]
        ]
    ];
    public function setUp(): void
    {
        parent::setUp();

        $this->addData('base');

        $this->tripService = app(CHTripsService::class);
        $this->flightService = app(FlightService::class);
        $this->pirepService = app(PirepService::class);
    }

    public function testCreateTrip()
    {
        // Setup Trip Fields
        $fields = $this->trip_case;

        // Test Procedure
        $trip = $this->tripService->createNewTrip($fields);

        // Assertions

        // Test Trip Created
        $this->assertModelExists($trip);

        // Test Flights Created
        $flights = Flight::where(['flight_number' => 1521])->get();
        $this->assertCount(2, $flights);

        // Test Flights Attached To Trip
        $this->assertCount(2, $trip->flights);

        $this->testTrip = $trip;
    }
    public function testAdvanceTripProgress()
    {
        // Setup
        $trip = $this->tripService->createNewTrip($this->trip_case);
        //dd(TripReport::all());
        // Test Procedure

        // Create and submit first pirep for trip.
        $pirep = Pirep::fromFlight($trip->flights()->first());
        $pirep->user_id = 1;
        $pirep->save();
        $this->pirepService->submit($pirep);

        // Assert

        // Check if PIREP attached to Pivot Table
        $fpt = FlightPirepTrip::where(['pirep_id' => $pirep->id])->first();
        //dd(FlightPirepTrip::all());
        $this->assertNotNull($fpt);

        //
    }
    public function testCompleteTrip()
    {
        // Setup
        $trip = $this->tripService->createNewTrip($this->trip_case);
        // Test Procedure
        // ==============

        // Foreach Leg, file a pirep
        foreach ($trip->fpts as $fpt)
        {
            $pirep = Pirep::fromFlight(Flight::find($fpt->flight_id));
            $pirep->user_id = 1;
            $pirep->save();
            $this->pirepService->submit($pirep);
        }

        // Assert
        // Once this happens all trip completion events should fire. Check for trip completion
        $trip->refresh();

        $this->assertEquals(TripState::COMPLETED, $this->testTrip->state);

        // Check if Flights were removed
        $this->assertCount(0, $this->testTrip->flights);

    }
}
