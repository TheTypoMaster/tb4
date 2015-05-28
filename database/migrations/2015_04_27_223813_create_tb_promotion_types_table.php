<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbPromotionTypesTable extends Migration {

	public function up()
	{
		Schema::create('tb_promotion_types', function(Blueprint $table) {
			$table->increments('promotion_type_id');
			$table->string('promotion_type_name', 128);
			$table->string('promotion_type_description');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_promotion_types');
	}
}