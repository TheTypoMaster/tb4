<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTournamentSponserFieldsToTournamentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_tournament', function(Blueprint $table) {
            $table->string('tournament_sponsor_name', 64)->after('free_credit_flag')->nullable;
            $table->string('tournament_sponsor_logo', 256)->after('tournament_sponsor_name')->nullable;
            $table->string('tournament_sponsor_logo_link', 256)->after('tournament_sponsor_logo')->nullable;
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
            $table->dropColumn('tournament_sponsor_name');
            $table->dropColumn('tournament_sponsor_logo');
            $table->dropColumn('tournament_sponsor_logo_link');
        });
    }

}
