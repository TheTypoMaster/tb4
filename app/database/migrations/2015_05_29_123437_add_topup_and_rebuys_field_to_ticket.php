<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTopupAndRebuysFieldToTicket extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_tournament_ticket', function(Blueprint $table)
		{
			$table->integer('rebuy_count')->default(0)->after('extra_starting_currency');
            $table->integer('topup_count')->default(0)->after('rebuy_count');
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
			$table->dropColumn('rebuy_count');
            $table->dropColumn('topup_count');
		});
	}

}
