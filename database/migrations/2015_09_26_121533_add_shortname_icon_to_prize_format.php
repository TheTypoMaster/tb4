<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShortnameIconToPrizeFormat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_tournament_prize_format', function (Blueprint $table) {
            $table->string('short_name')->after('name');
            $table->string('icon')->after('short_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_tournament_prize_format', function (Blueprint $table) {
            $table->dropColumn('short_name');
            $table->dropColumn('icon');
        });
    }
}
