<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAffiliateIdToTbdbTopbettaUserTable extends Migration {

	public function up()
	{

		Schema::table('tbdb_topbetta_user', function(Blueprint $table) {
			$table->integer('affiliate_id')->unsigned()->nullable()->after('btag');
		});


	}

	public function down()
	{
		Schema::table('tbdb_topbetta_user', function(Blueprint $table) {
			$table->dropColumn('affiliate_id');
		});
	}
}