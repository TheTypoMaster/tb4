<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIconToTournamentGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_tournament_groups', function (Blueprint $table) {
           $table->string('tournament_group_icon')->after('parent_group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_tournament_groups', function (Blueprint $table) {
            $table->dropColumn('tournament_group_icon');
        });
    }
}
