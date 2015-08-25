<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentEventGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_tournament_event_group', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->dateTime('start_date');
            $table->dateTime('end_date');


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
        Schema::drop('tb_tournament_event_group');
    }
}
