<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerenaIdToSportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_sports', function (Blueprint $table) {
            $table->integer('serena_sport_id')->unsigned()->index()->after('external_sport_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_sports', function (Blueprint $table) {
            $table->dropColumn('serena_sport_id');
        });
    }
}
