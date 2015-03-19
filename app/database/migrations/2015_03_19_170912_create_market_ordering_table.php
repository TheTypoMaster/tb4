<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketOrderingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_market_order', function(Blueprint $table)
		{
			$table->increments('id');

            $table->integer('sport_id')->unsigned();
            $table->integer('base_competition_id')->unsigned();

            $table->integer('user_id');

            $table->text('market_types');

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
		Schema::drop('tb_market_order');
	}

}
