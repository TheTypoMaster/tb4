<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateRisaFormTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('tb_data_risa_runner_form');

		Schema::create('tb_data_risa_runner_form', function(Blueprint $table) {
			$table->increments('id');
			$table->string('race_code')->index();
			$table->string('horse_code')->index();
			$table->string('runner_code')->index();
			$table->string('runner_name', 128);
			$table->string('last_starts_summary', 24)->nullable();
			$table->string('silk_image', 12)->nullable();
			$table->string('comment')->nullable();
			$table->string('career_results', 24)->nullable();
			$table->string('distance_results', 24)->nullable();
			$table->string('track_results', 24)->nullable();
			$table->string('track_distance_results', 24)->nullable();
			$table->string('first_up_results', 24)->nullable();
			$table->string('second_up_results', 24)->nullable();
			$table->string('good_results', 24)->nullable();
			$table->string('heavy_results', 24)->nullable();
			$table->string('firm_results', 24)->nullable();
			$table->string('soft_results', 24)->nullable();
			$table->string('synthetic_results', 24)->nullable();
			$table->string('wet_results', 24)->nullable();
			$table->string('nonwet_results', 24)->nullable();
			$table->string('night_results', 24)->nullable();
			$table->string('jumps_results', 24)->nullable();
			$table->string('season_results', 24)->nullable();

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
