<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbPromotionsUsersTable extends Migration {

	public function up()
	{
		Schema::create('tb_promotions_users', function(Blueprint $table) {
			$table->integer('promotion_id')->unsigned()->index();
			$table->integer('user_id')->unsigned()->index();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_promotions_users');
	}
}