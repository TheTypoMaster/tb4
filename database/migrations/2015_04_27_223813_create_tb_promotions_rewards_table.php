<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbPromotionsRewardsTable extends Migration {

	public function up()
	{
		Schema::create('tb_promotions_rewards', function(Blueprint $table) {
			$table->integer('promotion_id')->unsigned()->index();
			$table->integer('reward_id')->unsigned()->index();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_promotions_rewards');
	}
}