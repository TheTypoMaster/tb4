<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBetTypeIdToProductDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_product_default', function (Blueprint $table) {
            $table->integer('bet_type_id')->after('bet_type')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_product_default', function (Blueprint $table) {
            $table->dropColumn('bet_type_id');
        });
    }
}
