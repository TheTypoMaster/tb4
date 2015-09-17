<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFixedFlagToBetProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_bet_product', function (Blueprint $table) {
            $table->tinyInteger('is_fixed_odds')->after('name')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_bet_product', function (Blueprint $table) {
            $table->dropColumn('is_fixed_odds');
        });
    }
}
