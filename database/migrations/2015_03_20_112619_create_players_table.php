<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_players', function(Blueprint $table)
		{
			$table->increments('id');

            $table->integer('external_player_id');

            $table->string('name');
            $table->string('short_name');
            $table->string('default_name');

            $table->string('display_flag');

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
		Schema::drop('tb_players');
	}

}
