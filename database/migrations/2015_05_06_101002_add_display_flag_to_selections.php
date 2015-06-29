<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDisplayFlagToSelections extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_selection', function(Blueprint $table)
		{
			$table->tinyInteger('display_flag')->default(1);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_selection', function(Blueprint $table)
		{
			$table->dropColumn('display_flag');
		});
}

}
