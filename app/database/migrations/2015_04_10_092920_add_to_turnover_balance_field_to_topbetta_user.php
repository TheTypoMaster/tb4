<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToTurnoverBalanceFieldToTopbettaUser extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_topbetta_user', function(Blueprint $table)
		{
			$table->integer('balance_to_turnover')->after('betWinsToFbWall');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_topbetta_user', function(Blueprint $table)
		{
			$table->dropColumn('balance_to_turnover');
		});
	}

}
