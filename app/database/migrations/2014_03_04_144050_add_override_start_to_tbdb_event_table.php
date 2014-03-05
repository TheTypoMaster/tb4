<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddOverrideStartToTbdbEventTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_event', function(Blueprint $table) {
            $table->boolean('override_start')->after('paid_flag')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_event', function(Blueprint $table) {
            $table->dropColumn('override_start');
        });
    }

}
