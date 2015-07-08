<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailFlagToTournament extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_tournament', function(Blueprint $table)
		{
			$table->tinyInteger('email_flag')->default(true)->after('topup_end_date');
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
			$table->dropColumn('email_flag');
		});
	}

}
