<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbRewardsTable extends Migration {

	public function up()
	{
		Schema::create('tb_rewards', function(Blueprint $table) {
			$table->increments('id');
			$table->string('reward_name');
			$table->string('reward_description');
			$table->integer('reward_value');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_rewards');
	}
}