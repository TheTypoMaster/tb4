<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVenueIdToEventGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_event_group', function (Blueprint $table) {
            $table->integer('venue_id')->nullable()->unsigned()->after('base_competition_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_event_group', function (Blueprint $table) {
            $table->dropColumn('venue_id');
        });
    }
}
