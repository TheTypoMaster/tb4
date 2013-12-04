<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateTbdbEventGroupMarketTypeAddMarketId extends Migration {

     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::table('tbdb_event_group_market_type', function(Blueprint $table) {
    		$table->integer('market_id');
    	});
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::table('tbdb_event_group_market_type', function(Blueprint $table) {
    		$table->dropColumn('market_id');
    	});
    }

}
