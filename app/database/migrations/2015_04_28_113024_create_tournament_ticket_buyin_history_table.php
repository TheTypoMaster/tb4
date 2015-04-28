<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentTicketBuyinHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tbdb_tournament_ticket_buyin_history', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('tournament_ticket_id');
            $table->integer('tournament_buyin_type_id');
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
		Schema::drop('tbdb_tournament_ticket_buyin_history');
	}

}
