<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelectionCompetitorPivot extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_selection_competitor', function(Blueprint $table)
		{
			$table->increments('id');

            $table->integer('selection_id');

            $table->integer('competitor_id')->unsigned();

            $table->string('competitor_type');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tb_selection_competitor');
	}

}
