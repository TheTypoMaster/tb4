<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventPlayerTeamPivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_event_team_player', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('event_id')->unsigned()->index();
            $table->integer('team_id')->unsigned()->index();
            $table->integer('player_id')->unsigned()->index();

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
        Schema::drop('tb_event_team_player');
    }
}
