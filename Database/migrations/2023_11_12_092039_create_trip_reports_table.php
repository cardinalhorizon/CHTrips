<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateTripReportsTable
 */
class CreateTripReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ch_trip_reports', function (Blueprint $table) {
            $table->string('id');
            $table->foreignId('parent_id')->nullable();
            $table->string('parent_type')->nullable();
            $table->integer('trip_type');
            $table->string('name');
            $table->foreignId('owner_id');
            $table->integer('state');
            $table->foreignId('aircraft_id')->nullable();
            $table->timestamps();
        });
        Schema::create('ch_flight_pirep_trip', function (Blueprint $table) {
            $table->id();
            $table->string('trip_report_id');
            $table->string('flight_id');
            $table->string('pirep_id')->nullable();
            $table->integer('order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_reports');
    }
}
