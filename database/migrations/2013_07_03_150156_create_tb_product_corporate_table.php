<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbProductCorporateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_product_corporate', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('tb_product_id');
			$table->string('bet_type');
			$table->string('country');
			$table->string('region');
			$table->string('type_code');
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
		Schema::drop('tb_product_corporate');
	}

}
