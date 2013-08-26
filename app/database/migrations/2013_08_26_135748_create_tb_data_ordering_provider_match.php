<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbDataOrderOrdering extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_data_ordering_provider_match', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('provider_id')->index();
			$table->string('topbetta_keyword', 64)->index();
			$table->string('provider_value', 64)->index();
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
		Schema::drop('tb_data_ordering_provider_match');
	}

}
