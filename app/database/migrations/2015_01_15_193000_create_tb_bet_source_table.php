<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbBetSourceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_bet_source', function(Blueprint $table) {
			$table->increments('id');
			$table->string('keyword');
			$table->string('description');
			$table->string('api_endpoint')->nullable();
			$table->string('shared_secret')->nullable();
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
		Schema::drop('tb_bet_source');
	}

}
