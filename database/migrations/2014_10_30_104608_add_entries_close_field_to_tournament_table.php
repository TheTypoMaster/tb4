<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntriesCloseFieldToTournamentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::table('tbdb_tournament', function(Blueprint $table) {
            $table->dateTime('entries_close')->after('tournament_prize_format')->nullable();
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
            $table->dropColumn('entries_close');
        });
    }

}
