<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerenaIdToTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_teams', function (Blueprint $table) {
            $table->integer('serena_team_id')->unsigned()->index()->after('external_team_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_teams', function (Blueprint $table) {
            $table->dropColumn('serena_team_id');
        });
    }
}
