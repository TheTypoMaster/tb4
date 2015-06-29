<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutoIncrementToEge extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('tbdb_event_group_event', function(Blueprint $table) {

            DB::statement("ALTER TABLE tbdb_event_group_event ADD ege_id INT( 11 ) NOT NULL AUTO_INCREMENT FIRST, ADD UNIQUE (ege_id)");

            //$table->increments('ege_id')->before('event_group_id')->unique();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('tbdb_event_group_event', function(Blueprint $table) {
            $table->dropColumn('ege_id');
        });
	}

}
