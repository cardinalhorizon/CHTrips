<?php

namespace Modules\CHTrips\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Models\Enums\FlightType;
use App\Models\Flight;
use App\Models\Pirep;
use App\Repositories\AirlineRepository;
use App\Services\BidService;
use App\Services\FlightService;
use App\Services\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\CHTrips\Models\FlightPirepTrip;
use Modules\CHTrips\Models\TripReport;

/**
 * Class $CLASS$
 * @package
 */
class IndexController extends Controller
{
    public function __construct(
        public FlightService $flightService,
        public BidService $bidService,
        public AirlineRepository $airlineRepo,
        public ModuleService $moduleSvc
    ) {
    }
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $trips = TripReport::where('owner_id', Auth::user()->id)->with('fpts')->get();
        foreach($trips as $trip) {
            $completed = $trip->fpts->where('completed', true)->count();
            if (count($trip->fpts) == 0) {
                $trip->progress = 0;
                continue;
            }
            $prog = round($completed / count($trip->fpts) * 100);

            $trip->progress = "{$completed}/{$trip->fpts->count()} ({$prog})";
        }
        return view('chtrips::index', ['trips' => $trips]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function create(Request $request)
    {
        return view('chtrips::create', ['airlines' => $this->airlineRepo->selectBoxList(true)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        // Get the request
        $data = $request->all();
        $data['flight_number'] = random_int(9001, 9999);
        $data['minutes'] = 0;
        $data['hours'] = 0;
        $data['active'] = true;
        $data['visible'] = false;
        //dd($data);
        $airports = $data['airports'];
        $flight_type = $data['flight_type'];
        unset($data['flight_type']);
        unset($data['airports']);
        // First, create the trip.
        $tr = new TripReport();
        $tr->owner_id = $user->id;

        if ($data['name'] == "") {
            $tr->name = "Free Flight: {$airports[0]}->{$airports[count($airports) - 1]}";
        } else {
            $tr->name = $data['name'];
        }

        if ($data['description'] != "") {
            $tr->description = $data['description'];
        }

        $tr->save();

        // Now, create each flight based on the params
        for ($i = 0; $i < count($airports) - 1; $i++) {
            $data['dpt_airport_id'] = $airports[$i];
            $data['arr_airport_id'] = $airports[$i + 1];
            $data['route_leg'] = $i + 1;
            $data['flight_type'] = $flight_type[$i];
            //dd($data);
            //dd($tr);
            $flight = $this->flightService->createFlight($data);

            $flight->owner()->associate($tr);
            $flight->save();

            // TODO: Refactor this later when we find out WHY this is not behaving
            FlightPirepTrip::create([
                'trip_report_id' => $tr->id,
                'flight_id'      => $flight->id,
                'order'          => $i + 1
            ]);
            // $tr->flights()->attach($flight, ['order' => $i + 1]);
        }

        // Finally, add the flight to that user's bids.
        return to_route('chtrips.show', ['trip' => $tr->id]);
    }

    /**
     * Show the specified resource.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function show($trip, Request $request)
    {
        $trip_report = TripReport::find($trip);
        $upcoming = [];
        $completed = [];
        if ($trip_report === null) {
            abort(404, "Trip Report Not Found");
        }
        $fpts = $trip_report->fpts()->orderBy('order')->get();
        foreach ($fpts as $fpt) {
            if ($fpt->pirep_id === null) {
                //dd(Flight::find($fpt->flight_id));
                $upcoming[] = Flight::find($fpt->flight_id);
            } else {
                $completed[] = Pirep::find($fpt->pirep_id);
            }
        }
        //dd([$upcoming, $completed, $fpts]);
        $progress = round(count($completed) / count($fpts) * 100);
        return view('chtrips::show', [
            'name'          => $trip_report->name,
            'description'   => $trip_report->description,
            'flight'        => array_shift($upcoming),
            'progress'      => $progress,
            'upcoming'      => $upcoming,
            'pireps'        => $completed,
            'user'          => Auth::user(),
            'legs'          => $fpts->count(),
            'simbrief'      => !empty(setting('simbrief.api_key')),
            'simbrief_bids' => setting('simbrief.only_bids'),
            'acars_plugin'  => $this->moduleSvc->isModuleActive('VMSAcars'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function edit(Request $request)
    {
        return view('chtrips::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     */
    public function destroy(Request $request)
    {
    }
}
