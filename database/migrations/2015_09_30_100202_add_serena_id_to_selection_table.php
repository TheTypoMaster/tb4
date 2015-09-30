<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerenaIdToSelectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_selection', function (Blueprint $table) {
            $table->integer('serena_selection_id')->unsigned()->index()->after('external_selection_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_selection', function (Blueprint $table) {
            $table->dropColumn('serena_selection_id');
        });
    }
}
