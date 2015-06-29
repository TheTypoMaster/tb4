<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbProductProviderMatchTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_product_provider_match', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('tb_product_id');
			$table->integer('provider_id');
			$table->string('provider_product_name');
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
		Schema::drop('tb_product_provider_match');
	}

}
