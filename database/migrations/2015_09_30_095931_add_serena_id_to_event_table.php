<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerenaIdToEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_event', function (Blueprint $table) {
            $table->integer('serena_event_id')->unsigned()->index()->after('external_event_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_event', function (Blueprint $table) {
            $table->dropColumn('serena_event_id');
        });
    }
}
