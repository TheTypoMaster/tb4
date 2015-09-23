<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMarketGroupIdFieldToMarketTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_market_type', function (Blueprint $table) {
            $table->integer('market_type_group_id')->after('icon_id');
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
            $table->dropcolumn('market_type_group_id');
        });
    }
}
