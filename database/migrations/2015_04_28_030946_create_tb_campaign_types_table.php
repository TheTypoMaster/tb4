<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbCampaignTypesTable extends Migration {

	public function up()
	{
		Schema::create('tb_campaign_types', function(Blueprint $table) {
			$table->increments('campaign_type_id');
			$table->string('campaign_type_name');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_campaign_types');
	}
}