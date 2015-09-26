<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerenaIdToPlayers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_players', function (Blueprint $table) {
            $table->integer('serena_player_id')->unsigned()->index()->after('external_player_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_players', function (Blueprint $table) {
            $table->dropColumn('serena_player_id');
        });
    }
}
