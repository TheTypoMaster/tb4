<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTeamFieldToSelection extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_selection', function(Blueprint $table)
		{
			$table->integer('team_id')->after('wagering_api_id')->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_selection', function(Blueprint $table)
		{
			$table->dropColumn('team_id');
		});
	}

}
