<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_result_prices', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('event_id')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index();
            $table->integer('bet_type_id')->unsigned()->index();
            $table->integer('selection_result_id')->unsigned()->index();
            $table->float('dividend');
            $table->string('result_string');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tb_result_prices');
    }
}
