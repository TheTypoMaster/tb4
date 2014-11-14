<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('tbdb_users', function(Blueprint $table) {
            $table->string('remember_token', 100)->after('isTopBetta')->nullable();
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
            $table->dropColumn('remember_token');
        });
	}

}
