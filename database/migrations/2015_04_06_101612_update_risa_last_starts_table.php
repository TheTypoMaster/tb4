<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateRisaLastStartsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('tb_data_risa_runner_form_last_starts');

		Schema::create('tb_data_risa_runner_form_last_starts', function(Blueprint $table) {
			$table->increments('id');

			$table->integer('runner_form_id')->index();
			$table->string('race_code')->index();
			$table->string('horse_code')->index();
			$table->string('runner_code')->index();
			$table->string('finish_position', 12)->nullable();
			$table->integer('race_starters')->nullable();
			$table->string('abr_venue', 64)->nullable();
			$table->integer('race_distance')->nullable();
			$table->string('name_race_form', 32)->nullable();
			$table->date('mgt_date')->nullable();
			$table->string('track_condition', 12)->nullable();
			$table->integer('numeric_rating')->nullable();
			$table->string('jockey_initials', 12)->nullable();
			$table->string('jockey_surname', 24)->nullable();
			$table->decimal('handicap', 10, 2)->nullable();
			$table->integer('barrier')->nullable();
			$table->string('starting_win_price', 12)->nullable();
			$table->string('other_runner_name', 32)->nullable();
			$table->integer('other_runner_barrier')->nullable();
			$table->integer('in_running_800')->nullable();
			$table->integer('in_running_400')->nullable();
			$table->string('other_runner_time', 24)->nullable();
			$table->decimal('margin_decimal', 10, 2)->nullable();

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
