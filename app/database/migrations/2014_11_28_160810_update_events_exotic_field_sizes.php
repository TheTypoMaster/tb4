<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class UpdateEventsExoticFieldSizes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('ALTER TABLE `tbdb_event` MODIFY COLUMN `trifecta_dividend` VARCHAR(255)');
        DB::statement('ALTER TABLE `tbdb_event` MODIFY COLUMN `firstfour_dividend` VARCHAR(255)');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('ALTER TABLE `tbdb_event` MODIFY COLUMN `trifecta_dividend` VARCHAR(200)');
        DB::statement('ALTER TABLE `tbdb_event` MODIFY COLUMN `firstfour_dividend` VARCHAR(200)');
	}

}
