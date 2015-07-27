<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbDataRisaFormLastStartsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_data_risa_runner_form_last_starts', function(Blueprint $table) {
            $table->increments('id');
            
            $table->integer('runner_form_id')->index();
            $table->string('race_code', 24)->index();
            $table->string('horse_code', 24)->index();
            $table->string('runner_code', 64)->index();
            $table->string('finish_position', 12);
            $table->integer('race_starters');
            $table->string('abr_venue', 24);
            $table->integer('race_distance');
            $table->string('name_race_form', 32);
            $table->date('mgt_date');
            $table->string('track_condition', 12);
            $table->integer('numeric_rating');
            $table->string('jockey_initials', 12);
            $table->string('jockey_surname', 24);
            $table->decimal('handicap', 10, 2);
            $table->integer('barrier');
            $table->string('starting_win_price', 12);
            $table->string('other_runner_name', 32);
            $table->integer('other_runner_barrier');
            $table->integer('in_running_800');
            $table->integer('in_running_400');
            $table->string('other_runner_time', 24);
            $table->decimal('margin_decimal', 10, 2);
            
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
        Schema::drop('tb_data_risa_runner_form_last_starts');
    }

}
