<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PivotTbTeamTbdbEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_team_tbdb_event', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('tb_team_id')->unsigned()->index();
			$table->integer('tbdb_event_id')->unsigned()->index();
			$table->foreign('tb_team_id')->references('id')->on('tb_teams')->onDelete('cascade');
			$table->foreign('tbdb_event_id')->references('id')->on('tbdb_event')->onDelete('cascade');
		});
	}



	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tb_team_tbdb_event');
	}

}
