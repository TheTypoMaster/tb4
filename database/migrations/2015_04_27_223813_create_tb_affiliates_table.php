<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbAffiliatesTable extends Migration {

	public function up()
	{

		//Schema::drop('tb_affiliates');


		Schema::create('tb_affiliates', function(Blueprint $table) {
			$table->increments('affiliate_id');
			$table->integer('affiliate_type_id')->unsigned();
			$table->string('affiliate_name', 128);
			$table->string('affiliate_description')->nullable();
			$table->string('affiliate_btag')->nullable();
			$table->string('affiliate_code')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_affiliates');

	}
}