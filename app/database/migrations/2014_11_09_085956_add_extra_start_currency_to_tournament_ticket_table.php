<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraStartCurrencyToTournamentTicketTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::table('tbdb_tournament_ticket', function(Blueprint $table) {
            $table->integer('extra_starting_currency')->after('winner_alert_flag')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_tournament_ticket', function(Blueprint $table) {
            $table->dropColumn('extra_starting_currency');
        });
    }

}
