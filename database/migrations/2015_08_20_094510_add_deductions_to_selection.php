<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeductionsToSelection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_selection', function (Blueprint $table) {
            $table->integer('win_deductions')->unisigned();
            $table->integer('place_deductions')->unisigned();
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
            $table->dropColumn('win_deductions');
            $table->dropColumn('place_deductions');
        });
    }
}
