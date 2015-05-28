<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbPromotionsTable extends Migration {

	public function up()
	{



		Schema::create('tb_promotions', function(Blueprint $table) {
			$table->increments('promotion_id');
			$table->string('promotion_code', 64);
			$table->string('promotion_name', 128);
			$table->string('promotion_description')->nullable();
			$table->datetime('promotion_start_date')->nullable();
			$table->datetime('promotion_end_date')->nullable();
			$table->boolean('promotion_enabled')->default(0);
			$table->integer('promotion_usage_total')->unsigned();
			$table->integer('promotion_usage_unique')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_promotions');
	}
}