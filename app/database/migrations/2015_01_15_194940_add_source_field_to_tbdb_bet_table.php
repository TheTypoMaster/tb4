<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSourceFieldToTbdbBetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_bet', function(Blueprint $table) {
			$table->integer('bet_source_id')->after('bet_freebet_amount')->default(1);
		});

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_bet', function(Blueprint $table) {
			$table->dropColumn('bet_source_id');
		});
	}

}
