<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTbdbMarketTypeTablev2 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
		
	public function up()
	{
		Schema::table('tbdb_market_type', function(Blueprint $table)
		{
			$table->integer('external_bet_type_id');
			
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_market_type', function($table)
		{
	
		});
	}
	
}
