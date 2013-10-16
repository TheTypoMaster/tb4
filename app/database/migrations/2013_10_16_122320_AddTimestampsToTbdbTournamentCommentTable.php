<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTimestampsToTbdbTournamentCommentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('tbdb_tournament_comment', function(Blueprint $table) {                        
            DB::statement("ALTER TABLE `tbdb_tournament_comment` MODIFY created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL;");
            DB::statement("ALTER TABLE `tbdb_tournament_comment` MODIFY updated_date TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL;");
            $table->timestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}