<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRunnersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_runners', function(Blueprint $table)
		{
			$table->increments('id');

            $table->integer('external_runner_id')->unsigned();
            $table->integer('owner_id')->unsigned()->default(0);
            $table->integer('trainer_id')->unsigned()->default(0);
            $table->string('name');
            $table->string('colour');
            $table->char('sex');
            $table->integer('age');
            $table->date('foal_date');
            $table->string('sire');
            $table->string('dam');

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
		Schema::drop('tb_runners');
	}

}
