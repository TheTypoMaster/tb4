<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbRacingDataRisaSilkMapTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_racing_data_risa_silk_map', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('runner_code', 128);
			$table->string('silk_file_name', 32);
			$table->string('last_starts', 32);
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
		Schema::drop('tb_racing_data_risa_silk_map');
	}

}
