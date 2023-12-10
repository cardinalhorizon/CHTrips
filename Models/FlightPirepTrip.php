<?php

namespace Modules\CHTrips\Models;

use App\Contracts\Model;
use App\Models\Flight;

/**
 * Class FlightPirepTrip
 * @package Modules\CHTrips\Models
 */
class FlightPirepTrip extends Model
{
    public $table = 'ch_flight_pirep_trip';
    protected $fillable = [
        'id',
        'trip_report_id',
        'flight_id',
        'pirep_id',
        'order'
    ];
    public $timestamps = false;
    public function flight() {
        return $this->belongsTo(Flight::class);
    }
}
