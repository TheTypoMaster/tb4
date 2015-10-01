<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFixedOddsDisplayFlagToTbdbEventGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_event_group', function (Blueprint $table) {
            $table->boolean('fixed_odds_enabled')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_event_group', function (Blueprint $table) {
            $table->dropColumn('fixed_odds_enabled');
        });
    }
}
