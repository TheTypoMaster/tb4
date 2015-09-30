<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerenaIdToMarketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_market', function (Blueprint $table) {
            $table->integer('serena_market_id')->unsigned()->after('external_market_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_market', function (Blueprint $table) {
            $table->dropColumn('serena_market_id');
        });
    }
}
