<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFreeCreditWinsToTurnoverToTopbettaUser extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_topbetta_user', function(Blueprint $table)
		{
			$table->integer('free_credit_wins_to_turnover')->after('balance_to_turnover')->default(0);
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
			$table->dropColumn('free_credit_wins_to_turnover');
		});
	}

}
