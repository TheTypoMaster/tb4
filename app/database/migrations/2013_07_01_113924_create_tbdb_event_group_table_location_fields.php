<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbdbEventGroupTableLocationFields extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_event_group', function(Blueprint $table) 
        {
           $table->string('country');
           $table->string('meeting_grade');
           $table->string('rail_position');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_event_group', function($table)
		{

		});
    }

}
