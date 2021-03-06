<?php namespace TopBetta\Models;

use Eloquent;

class RaceMeeting extends Eloquent {

	protected $table = 'tbdb_event_group';

	public function raceevents() {
		return $this -> belongsToMany('TopBetta\Models\RaceEvent', 'tbdb_event_group_event', 'event_group_id', 'event_id')
		 -> join('tbdb_event_status', 'tbdb_event.event_status_id', '=', 'tbdb_event_status.id')
		 -> where('tbdb_event.event_status_id', '!=', '7')
		 -> select(array('tbdb_event.*', 'tbdb_event_status.name AS status'));
	}


	/**
	 * Grab details of meeting based on external ID.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */	
	static public function getMeetingDetails($meetingId) {
		$racingCodes = array('R', 'G', 'H');
		return RaceMeeting::where('external_event_group_id', '=', $meetingId)
		->whereIn('type_code', $racingCodes)->get()->toArray();
	}


	/**
	 * Check if a meeting exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function meetingExists($meetingId) {
		return RaceMeeting::where('external_event_group_id', '=', $meetingId)
							->where('sport_id', '0')-> value('id');
	}
	
	static public function meetingExistsByCode($meetingCode) {
		return RaceMeeting::where('meeting_code', '=', $meetingCode)-> value('id');
	}
	
	static public function isRace($meetingId) {
		$type_code = RaceMeeting::where('external_event_group_id', '=', $meetingId)->value('type_code');
		return ($type_code == 'NULL' ? false : true);
	}

	static public function isInternational($meetingId) {
		$countryCode = RaceMeeting::where('id', '=', $meetingId)->value('country');
		return $countryCode !== 'AU' && $countryCode !== 'NZ';
	}

	
	static public function getRacesForMeetingId($meetingId, $displayOnly = true) {
		$races = RaceMeeting::find($meetingId) -> raceevents;

		$result = array();

		foreach ($races as $race) {

			if($displayOnly && $race->display_flag == 0) continue;

			$resultsModel = new RaceResult;
			$results = $resultsModel -> getResultsForRaceId($race -> id);	
			
			$toGo = \TopBetta\Helpers\TimeHelper::nicetime(strtotime($race -> start_date), 2);

			//convert the date to ISO 8601 format
			$startDatetime = new \DateTime($race -> start_date);
			$startDatetime = $startDatetime -> format('c');	
			
			$updatedAt = $race -> updated_at;
			if ($updatedAt -> year > 0) {
						
				$updatedAt = $updatedAt->toISO8601String();		
				
			} else {
				
				$updatedAt = false;
				
			}			

			$result[] = array('id' => $race -> id, 'display' => $race->display_flag, 'external_race_id' => $race->external_event_id, 'race_number' => $race -> number, 'to_go' => $toGo, 'name' => $race -> name, 'distance' => $race -> distance, 'class' => $race->class, 'start_datetime' => $startDatetime, 'updated_at' => $updatedAt, 'results' => $results, 'status' => $race -> status);

		}	
		
		return $result;	
	}

    public function sport()
    {
        return $this->belongsTo('TopBetta\Models\SportModel', 'sport_id');
    }


}
