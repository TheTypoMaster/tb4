<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopbettaAffiliates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tbdb_affiliates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('company_name');
			$table->string('affiliate_id');
			$table->string('campaign_id');
			$table->text('filter');			
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tbdb_affiliates');
	}

}
