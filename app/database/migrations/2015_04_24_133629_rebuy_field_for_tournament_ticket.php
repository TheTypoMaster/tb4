<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RebuyFieldForTournamentTicket extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_tournament_ticket', function(Blueprint $table)
		{
			$table->integer('rebuys')->default(0)->after('extra_starting_currency');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_tournament_ticket', function(Blueprint $table)
		{
			$table->dropColumn('rebuys');
		});
	}

}
