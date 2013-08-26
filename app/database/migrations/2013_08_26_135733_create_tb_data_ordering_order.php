<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbDataOrderValues extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_data_ordering_order', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('sport_keyword', 64)->index();
			$table->string('topbetta_keyword', 64)->index();
			$table->integer('order_number');
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
		Schema::drop('tb_data_ordering_order');
	}

}
