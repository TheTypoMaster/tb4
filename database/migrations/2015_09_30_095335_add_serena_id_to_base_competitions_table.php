<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerenaIdToBaseCompetitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_base_competition', function (Blueprint $table) {
            $table->integer('serena_base_competition_id')->unsigned()->index()->after('external_base_competition_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_base_competition', function (Blueprint $table) {
            $table->dropColumn('serena_base_competition_id');
        });
    }
}
