<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateTrainerFieldInSelectionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tbdb_selection', function(Blueprint $table) {
			DB::statement('ALTER TABLE `tbdb_selection` MODIFY `last_starts` VARCHAR(32)');
			DB::statement('ALTER TABLE `tbdb_selection` MODIFY `trainer` VARCHAR(32)');
			DB::statement('ALTER TABLE `tbdb_selection` MODIFY `home_away` VARCHAR(32)');
			DB::statement('ALTER TABLE `tbdb_selection` MODIFY `bet_type_ref` VARCHAR(24)');
			DB::statement('ALTER TABLE `tbdb_selection` MODIFY `bet_place_ref` VARCHAR(24)');
			DB::statement('ALTER TABLE `tbdb_selection` MODIFY `runner_code` VARCHAR(64)');
			DB::statement('ALTER TABLE `tbdb_selection` MODIFY `image_url` VARCHAR(255)');

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
			$table->dropColumn('activated_flag');
		});
	}

}
