<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFreeTournamentLimitAppliesFlag extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_tournament', function(Blueprint $table)
		{
			$table->tinyInteger('free_tournament_buyin_limit_flag')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_tournament', function(Blueprint $table)
		{
            $table->dropColumn('free_tournament_buyin_limit_flag');
		});
	}

}
