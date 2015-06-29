<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQualifiedFieldToTournamentLeaderboard extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_tournament_leaderboard', function(Blueprint $table)
		{
			$table->integer('balance_to_turnover')->after('turned_over')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_tournament_leaderboard', function(Blueprint $table)
		{
			$table->dropColumn('balance_to_turnover');
		});
	}

}
