<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbRacingDataRisaListTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_racing_data_risa_list', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('file_name', 256);
			$table->boolean('processed');
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
		Schema::drop('tb_racing_data_risa_list');
	}

}
