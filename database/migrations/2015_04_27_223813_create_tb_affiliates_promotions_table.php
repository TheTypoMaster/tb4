<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbAffiliatesPromotionsTable extends Migration {

	public function up()
	{
		Schema::create('tb_affiliates_promotions', function(Blueprint $table) {
			$table->integer('affiliate_id')->unsigned()->index();
			$table->integer('promotion_id')->unsigned()->index();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_affiliates_promotions');
	}
}