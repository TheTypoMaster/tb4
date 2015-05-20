<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFieldsToTbdbBetOrigin extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_bet_origin', function(Blueprint $table) {
			$table->string('api_endpoint')->after('description')->nullable();
			$table->string('shared_secret')->after('api_endpoint')->nullable();

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_bet_origin', function(Blueprint $table) {
			$table->dropColumn('api_endpoint');
			$table->dropColumn('shared_secret');
		});
	}

}
