<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_event', function(Blueprint $table)
		{
			$table->string('short_name')->after('name');
            $table->string('default_name')->after('short_name');

            $table->string('description')->after('default_name');

            $table->integer('icon_id')->after('wagering_api_id')->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_event', function(Blueprint $table)
		{
			$table->dropColumn('short_name');
            $table->dropColumn('default_name');
            $table->dropColumn('description');
            $table->dropColumn('icon_id');
		});
	}

}
