<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbDataRisaFormTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_data_risa_runner_form', function(Blueprint $table) {
            $table->increments('id');
            
            $table->string('race_code', 24)->index();
            $table->string('horse_code', 24)->index();
            $table->string('runner_code', 64)->index();
            $table->string('runner_name', 128);
            $table->string('age', 12);
            $table->string('sex', 12);
            $table->string('colour', 24);
            $table->string('career_results', 24);
            $table->string('track_results', 24);
           	$table->string('track_distance_results', 24);
           	$table->string('first_up_results', 24);
           	$table->string('second_up_results', 24);
           	$table->string('good_results', 24);
           	$table->string('dead_results', 24);
           	$table->string('slow_results', 24);
           	$table->string('heavy_results', 24);
           	$table->string('last_starts_summary', 24);
           	$table->string('silk_image', 12);
           	$table->string('comment', 256);
              
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
        Schema::drop('tb_data_risa_runner_form');
    }

}
