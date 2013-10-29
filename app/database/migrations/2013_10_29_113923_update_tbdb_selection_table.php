<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateTbdbSelectionTablev3 extends Migration {

/**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_selection', function(Blueprint $table) {
            $table->string('runner_code', 24);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_selection', function(Blueprint $table) {
            
        });
    }
	
}
