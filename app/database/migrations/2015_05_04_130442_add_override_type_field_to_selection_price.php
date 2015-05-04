<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOverrideTypeFieldToSelectionPrice extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_selection_price', function(Blueprint $table)
		{
			$table->string('override_type')->nullable()->after('override_odds');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_selection_price', function(Blueprint $table)
		{
			$table->dropColumn('override_type');
		});
	}

}
