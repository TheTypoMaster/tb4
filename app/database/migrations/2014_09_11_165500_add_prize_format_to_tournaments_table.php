<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPrizeFormatToTournamentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_tournament', function(Blueprint $table) {
            $table->string('tournament_prize_format', 64)->after('tournament_sponsor_logo_link')->default(3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_tournament', function(Blueprint $table) {
            $table->dropColumn('tournament_prize_format');
        });
    }

}