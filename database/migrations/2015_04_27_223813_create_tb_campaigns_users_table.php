<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbCampaignsUsersTable extends Migration {

	public function up()
	{
		Schema::create('tb_campaigns_users', function(Blueprint $table) {
			$table->integer('campain_id')->unsigned()->index();
			$table->integer('user_id')->unsigned()->index();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_campaigns_users');
	}
}