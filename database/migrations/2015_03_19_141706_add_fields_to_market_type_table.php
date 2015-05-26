<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToMarketTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_market_type', function(Blueprint $table)
		{
			$table->string('short_name')->after('name');
            $table->string('default_name')->after('short_name');

            $table->text('market_rules')->after('description');

            $table->tinyInteger('display_flag')->after('status_flag');

            $table->integer('icon_id')->after('display_flag')->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_market_type', function(Blueprint $table)
		{
			$table->dropColumn('short_name');
            $table->dropColumn('default_name');
            $table->dropColumn('market_rules');
            $table->dropColumn('display_flag');
            $table->dropColumn('icon_id');
		});
	}

}
