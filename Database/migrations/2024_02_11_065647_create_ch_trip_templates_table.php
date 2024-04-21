<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateChTripTemplatesTable
 */
class CreateChTripTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Allow Multiple Users to be assigned to a trip.
        Schema::create('trip_report_user', function (Blueprint $table) {
            $table->string('trip_report_id');
            $table->foreignId('user_id');
            $table->boolean('owner');
        });
        // Convert Users from old trip ownership to new one.
        //$tr = \Modules\CHTrips\Models\TripReport::all();
        //$user_id = $tr->owner_id;
        //$tr->users()->attach($user_id, ['owner' => true]);

        // Add Trip Templates
        Schema::create('ch_trip_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('visible')->default(true);
            $table->boolean('enabled')->default(true);
            $table->longText('description')->nullable();
            $table->integer('type')->nullable();
            $table->json('data'); // List of all the flights to be generated for this trip.
            $table->date('starting_at')->nullable();
            $table->date('ending_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('ch_trip_reports', function (Blueprint $table) {
            $table->boolean('can_duplicate'); // Allows trip to be duplicated by another user so they can fly a similar trip.
            //$table->foreignId('owner_id')->nullable()->change(); // Allow trip to be claimed by users.
            $table->dropColumn('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ch_trip_templates');
    }
}
