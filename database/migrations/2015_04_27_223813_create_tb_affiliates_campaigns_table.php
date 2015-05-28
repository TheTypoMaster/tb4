<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbAffiliatesCampaignsTable extends Migration {

	public function up()
	{
		Schema::create('tb_affiliates_campaigns', function(Blueprint $table) {
			$table->integer('affiliate_id')->unsigned()->index();
			$table->integer('campaign_id')->unsigned()->index();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_affiliates_campaigns');
	}
}