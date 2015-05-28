<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbCampaignsTable extends Migration {

	public function up()
	{
		Schema::create('tb_campaigns', function(Blueprint $table) {
			$table->increments('campaign_id');
			$table->string('campaign_name');
			$table->string('campaign_source');
			$table->string('campaign_medium')->nullable();
			$table->string('campaign_term')->nullable();
			$table->string('campaign_contemt')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_campaigns');
	}
}