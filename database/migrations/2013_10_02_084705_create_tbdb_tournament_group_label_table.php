<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbdbTournamentGroupLabelTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_tournament_group_label', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('tournament_label_id')->index();
            $table->integer('tournament_group_id')->index();
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
        Schema::drop('tb_tournament_group_label');
    }

}
