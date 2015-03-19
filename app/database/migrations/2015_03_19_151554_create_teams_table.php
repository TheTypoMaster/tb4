<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_teams', function(Blueprint $table)
		{
			$table->increments('id');

            $table->string('name');
            $table->string('short_name');
            $table->string('long_name');

            $table->string('description');

            $table->tinyInteger('display_flag');

            $table->integer('icon_id')->unsigned();

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
		Schema::drop('tb_teams');
	}

}
