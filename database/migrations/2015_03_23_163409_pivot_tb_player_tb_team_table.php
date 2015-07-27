<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PivotTbPlayerTbTeamTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_player_tb_team', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('tb_player_id')->unsigned()->index();
			$table->integer('tb_team_id')->unsigned()->index();
			$table->foreign('tb_player_id')->references('id')->on('tb_players')->onDelete('cascade');
			$table->foreign('tb_team_id')->references('id')->on('tb_teams')->onDelete('cascade');
		});
	}



	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tb_player_tb_team');
	}

}
