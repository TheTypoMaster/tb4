<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbAffiliateTypesTable extends Migration {

	public function up()
	{
		Schema::create('tb_affiliate_types', function(Blueprint $table) {
			$table->increments('affilaite_type_id');
			$table->string('affiliate_type_name');
			$table->string('affiliate_type_description')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_affiliate_types');
	}
}