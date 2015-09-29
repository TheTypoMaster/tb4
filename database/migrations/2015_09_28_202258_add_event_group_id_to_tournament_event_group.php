<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventGroupIdToTournamentEventGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_tournament_event_group', function (Blueprint $table) {
            $table->integer('event_group_id')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_tournament_event_group', function (Blueprint $table) {
            $table->dropColumn('event_group_id');
        });
    }
}
