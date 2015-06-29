<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBetLimitToTbdbTournmamentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_tournament', function(Blueprint $table) {
            $table->integer('bet_limit_per_event')->after('bet_limit_flag')->nullable();
			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_tournament', function(Blueprint $table) {
            $table->dropColumn('bet_limit_per_event');
		});
	}

}
