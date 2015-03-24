<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompetitionRegionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_competition_region', function(Blueprint $table)
		{
			$table->increments('id');

            $table->integer('external_competition_region_id')->unsigned();

            $table->string('name');
            $table->string('short_name');
            $table->string('default_name');

            $table->string('description');

            $table->tinyInteger('display_flag');

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
		Schema::drop('tb_competition_region');
	}

}