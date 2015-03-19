<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToEventGroup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_event_group', function(Blueprint $table)
		{
			$table->integer('base_competition_id')->after('wagering_api_id')->unsigned();

            $table->string('short_name')->after('name');

            $table->string('default_name')->after('short_name');

            $table->string('description')->after('default_name');

            $table->integer('icon_id')->after('display_flag')->unsigned();

            $table->integer('default_event_icon_id')->after('icon_id')->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_event_group', function(Blueprint $table)
		{
			$table->dropColumn('base_competition_id');
            $table->dropColumn('short_name');
            $table->dropColumn('default_name');
            $table->dropColumn('description');
            $table->dropColumn('icon_id');
            $table->dropColumn('default_event_icon_id');
		});
	}

}
