<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbRewardTypesTable extends Migration {

	public function up()
	{
		Schema::create('tb_reward_types', function(Blueprint $table) {
			$table->increments('reward_id');
			$table->string('reward_type_name');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_reward_types');
	}
}