<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddParentUserIdToUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_users', function(Blueprint $table) {
			$table->integer('parent_user_id')->after('betWinsToFbWall')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_users', function(Blueprint $table) {
			$table->dropColumn('parent_user_id');
		});
	}

}
