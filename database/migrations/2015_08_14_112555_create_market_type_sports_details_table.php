<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketTypeSportsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_sport_market_type_details', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('sport_id')->unsigned()->index();
            $table->integer('market_type_id')->unsigned()->index();
            $table->integer('max_winning_selections');

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
        Schema::drop('tb_sport_market_type_details');
    }
}
