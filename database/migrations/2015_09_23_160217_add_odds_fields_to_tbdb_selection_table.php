<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOddsFieldsToTbdbSelectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_selection', function (Blueprint $table) {
            $table->string('apn_fluctuations');
            $table->string('topbetta_fluctuations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_selection', function (Blueprint $table) {
            $table->dropcolumn('apn_fluctuations');
            $table->dropcolumn('topbetta_fluctuations');
        });
    }
}
