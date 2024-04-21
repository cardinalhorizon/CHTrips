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
        if (!Schema::hasTable('ch_trip_reports')) {
        Schema::create('ch_trip_reports', function (Blueprint $table) {
            $table->string('id');
            $table->foreignId('parent_id')->nullable();
            $table->string('parent_type')->nullable();
            $table->longText('description')->nullable();
            $table->string('name');
            $table->integer('state');
            $table->foreignId('aircraft_id')->nullable();
            $table->timestamps();
        });
        }
        if (!Schema::hasTable('ch_flight_pirep_trip')) {
        Schema::create('ch_flight_pirep_trip', function (Blueprint $table) {
            $table->id();
            $table->string('trip_report_id');
            $table->string('flight_id');
            $table->string('pirep_id')->nullable();
            $table->integer('order');
        });
        }
        if (!Schema::hasTable('trip_report_user')) {
        Schema::create('trip_report_user', function (Blueprint $table) {
            $table->string('trip_report_id');
            $table->foreignId('user_id');
            $table->boolean('owner');
        });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ch_trip_reports');
        Schema::dropIfExists('ch_flight_pirep_trip');
        Schema::dropIfExists('trip_report_user');
    }
}
