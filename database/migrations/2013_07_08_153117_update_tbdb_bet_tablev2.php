<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTbdbBetTablev2 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_bet', function(Blueprint $table)
		{
			$table->integer('boxed_flag');
			$table->integer('combinations');
			$table->decimal('percentage');
			$table->string('selection_string');
			$table->string('line');
			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_bet', function($table)
		{
			
		});
	}

}
