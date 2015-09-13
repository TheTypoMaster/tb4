<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultEventGroupProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_default_event_group_product', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('event_group_id')->unsigned()->index();
            $table->integer('bet_product_id')->unsigned()->index();
            $table->integer('bet_type_id')->unsigned()->index();

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
        Schema::drop('tb_default_event_group_product');
    }
}
