<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToTournamentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_tournament', function (Blueprint $table) {
            $table->string('tournament_type', 32)->after('free_tournament_buyin_limit_flag');
            $table->boolean('tournament_mixed')->default(0)->after('free_tournament_buyin_limit_flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_tournament', function (Blueprint $table) {
            $table->dropColumn('tournament_type');
            $table->dropColumn('tournament_mixed');
        });
    }
}
