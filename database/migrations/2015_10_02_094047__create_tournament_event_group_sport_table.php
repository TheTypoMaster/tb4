<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentEventGroupSportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_tournament_event_group_sport', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tournament_event_group_id');
            $table->integer('sport_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tb_tournament_event_group_sport');
    }
}
