<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentGroupPivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_tournament_group_tournament', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('tournament_group_id')->unsigned()->index();

            $table->integer('tournament_id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tb_tournament_group_tournament');
    }
}
