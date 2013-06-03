<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbRacingDataRisaMeetingDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_racing_data_risa_meeting_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->int('meeting_code')->unique();
			$table->string('code_type');
			$table->string('meeting_category');
			$table->string('meeting_stage');
			$table->string('meeting_stage_encoded');
			$table->string('meeting_phase');
			$table->int('phase_meeting_encoded');
			$table->dateTime('nominations_close');
			$table->dateTime('acceptance_close');
			$table->dateTime('riders_close');
			$table->dateTime('weights_publishing');
			$table->int('venue_code');
			$table->string('venue_name');
			$table->string('venue_abbr');
			$table->int('club_code');
			$table->string('club_title');
			$table->string('club_abbr');
			$table->string('club_state');
			$table->string('club_category');
			$table->string('meeting_type');
			$table->string('apprentice_meeting_type');
			$table->date('meet_date');
			$table->string('state_desc');
			$table->int('number_of_races');
			$table->string('rail_position');
			$table->string('day_night');
			$table->string('tab_status');
			$table->string('weather');
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
		Schema::drop('tb_racing_data_risa_meeting_details');
	}

}
