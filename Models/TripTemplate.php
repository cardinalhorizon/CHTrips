<?php

namespace Modules\CHTrips\Models;

use App\Contracts\Model;

/**
 * Class TripTemplate
 * @package Modules\CHTrips\Models
 */
class TripTemplate extends Model
{
    public $table = 'ch_trip_templates';
    protected $fillable = ['name', 'description', 'type', 'data'];

    public function trip_reports()
    {
        return $this->morphMany(TripReport::class, 'parent');
    }
}
