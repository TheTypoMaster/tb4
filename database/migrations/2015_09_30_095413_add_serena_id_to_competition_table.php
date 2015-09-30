<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerenaIdToCompetitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_event_group', function (Blueprint $table) {
            $table->string('serena_event_group_id')->index()->after('external_event_group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_event_group', function (Blueprint $table) {
            $table->dropColumn('serena_event_group_id');
        });
    }
}
