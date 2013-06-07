<?php
namespace TopBetta;
use Doctrine\Tests\DBAL\Types\IntegerTest;

class RaceMeeting extends \Eloquent {

	protected $table = 'tbdb_event_group';

	public function raceevents() {
		return $this -> belongsToMany('TopBetta\RaceEvent', 'tbdb_event_group_event', 'event_group_id', 'event_id')
		 -> join('tbdb_event_status', 'tbdb_event.event_status_id', '=', 'tbdb_event_status.id')
		 -> select(array('tbdb_event.*', 'tbdb_event_status.name AS status'));
	}

	/**
	 * Check if a meeting exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function meetingExists($meetingId) {
		return RaceMeeting::where('external_event_group_id', '=', $meetingId) -> pluck('id');
	}
	
	static public function getRacesForMeetingId($meetingId) {
		$races = RaceMeeting::find($meetingId) -> raceevents;

		$result = array();

		foreach ($races as $race) {
				
			$resultsModel = new \TopBetta\RaceResult; 	
			$results = $resultsModel -> getResultsForRaceId($race -> id);	
			
			$toGo = \TimeHelper::nicetime(strtotime($race -> start_date), 2);

			//convert the date to ISO 8601 format
			$startDatetime = new \DateTime($race -> start_date);
			$startDatetime = $startDatetime -> format('c');				

			$result[] = array('id' => $race -> id, 'race_number' => $race -> number, 'to_go' => $toGo, 'start_datetime' => $startDatetime, 'results' => $results, 'status' => $race -> status);

		}	
		
		return $result;	
	}

}
