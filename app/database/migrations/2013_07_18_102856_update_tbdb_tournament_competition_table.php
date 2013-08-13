<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTbdbTournamentCompetitionTable extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_tournament_competition', function(Blueprint $table)
		{
			$table->timestamps();
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_tournament_competition', function($table)
		{
				
		});
	}
	

}
