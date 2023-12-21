<?php

namespace Modules\CHTrips\Models;

use App\Contracts\Model;
use App\Models\Flight;
use App\Models\Pirep;
use App\Models\Traits\HashIdTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TripReport
 * @package Modules\CHTrips\Models
 * @property int state
 * @property string id
 * @property string name
 */
class TripReport extends Model
{
    use HashIdTrait;
    public $table = 'ch_trip_reports';

    protected $keyType = 'string';
    public $incrementing = false;

    public function parent(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function fpts() {
        return $this->hasMany(FlightPirepTrip::class);
    }
    public function pireps()
    {
        return $this->belongsToMany(Pirep::class, 'ch_flight_pirep_trip')->withPivot('order');
    }
    public function flights()
    {
        return $this->belongsToMany(Flight::class, 'ch_flight_pirep_trip')->withPivot('order');
    }

    protected $casts = [

    ];

    public static $rules = [

    ];
}
