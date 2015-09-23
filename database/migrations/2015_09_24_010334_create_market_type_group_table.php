<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketTypeGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_market_type_groups', function (Blueprint $table) {
            $table->increments('market_type_group_id');
            $table->string('market_type_group_name');
            $table->string('market_type_group_description');
            $table->bool('market_type_group_display_flag')->default(0);
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
        Schema::drop('tb_market_type_groups');
    }
}
