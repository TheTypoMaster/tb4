<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerenaIdToMarketTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_market_type', function (Blueprint $table) {
            $table->integer('serena_market_type_id')->unsigned()->index()->after('external_bet_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_market_type', function (Blueprint $table) {
            $table->dropColumn('serena_market_type_id');
        });
    }
}
