<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbCampaignsPromotionsTable extends Migration {

	public function up()
	{
		Schema::create('tb_campaigns_promotions', function(Blueprint $table) {
			$table->integer('campaign_id')->unsigned()->index();
			$table->integer('promotion_id')->unsigned()->index();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_campaigns_promotions');
	}
}