<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RebuyFieldsForTbdbTournament extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_tournament', function(Blueprint $table)
		{
            // --- Rebuy Info ---
            $table->integer('rebuys')->after('entries_close');
            $table->integer('rebuy_entry')->after('rebuys');
            $table->integer('rebuy_buyin')->after('rebuy_entry');
            $table->integer('rebuy_currency')->after('rebuy_buyin');
            $table->dateTime('rebuy_end')->nullable()->after('rebuy_currency');

            // --- Addon Info ---
            $table->integer('topups')->after('rebuy_end');
            $table->integer('topup_entry')->after('topups');
            $table->integer('topup_buyin')->after('topup_entry');
            $table->integer('topup_currency')->after('topup_buyin');
            $table->dateTime('topup_start_date')->nullable()->after('topup_currency');
            $table->dateTime('topup_end_date')->nullable()->after('topup_start_date');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_tournament', function(Blueprint $table)
		{
            // --- Rebuy Info ---
            $table->dropColumn('rebuys');
            $table->dropColumn('rebuy_entry');
            $table->dropColumn('rebuy_buyin');
            $table->dropColumn('rebuy_currency');
            $table->dropColumn('rebuy_end');

            // --- Addon Info ---
            $table->dropColumn('topups');
            $table->dropColumn('topup_entry');
            $table->dropColumn('topup_buyin');
            $table->dropColumn('topup_currency');
            $table->dropColumn('topup_start_date');
            $table->dropColumn('topup_end_date');
            
		});
	}

}
