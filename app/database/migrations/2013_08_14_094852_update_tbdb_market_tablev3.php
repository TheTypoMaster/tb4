<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTbdbMarketTablev3 extends Migration {


	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_market', function(Blueprint $table)
		{
			$table->integer('pitcher_home_no');
			$table->string('pitcher_home_name');
			$table->integer('pitcher_away_no');
			$table->string('pitcher_away_name');
			$table->string('market_status');
			$table->string('period');
			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_market', function($table)
		{
			//
		});
	}
	

}






