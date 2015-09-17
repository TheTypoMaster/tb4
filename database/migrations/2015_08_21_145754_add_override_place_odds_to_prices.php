<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOverridePlaceOddsToPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_selection_price', function (Blueprint $table) {
            $table->float('override_place_odds');
            $table->string('override_place_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_selection_price', function (Blueprint $table) {
            $table->dropColumn('override_place_odds');
            $table->dropColumn('override_place_type');
        });
    }
}
