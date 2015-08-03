<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderingFieldToTournamentGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_tournament_groups', function (Blueprint $table) {

            $table->integer('ordering')->nullable();

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
            $table->dropColumn('ordering');
        });
    }
}
