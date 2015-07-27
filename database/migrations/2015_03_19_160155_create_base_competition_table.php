<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaseCompetitionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_base_competition', function(Blueprint $table)
		{
			$table->increments('id');

            $table->integer('external_base_competition_id')->unsigned();

            $table->integer('sport_id')->unsigned();

            $table->integer('region_id')->unsigned();

            $table->string('name');
            $table->string('short_name');
            $table->string('default_name');

            $table->string('description');

            $table->tinyInteger('display_flag');

            $table->integer('icon_id')->unsigned();

            $table->integer('default_event_group_icon_id')->unsigned();

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
		Schema::drop('tb_base_competition');
	}

}
