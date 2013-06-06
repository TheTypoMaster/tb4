<?php
namespace TopBetta;

class SportsEvents extends \Eloquent {
	
	protected $table = 'tbdb_event';
	
	protected $guarded = array();

	public static $rules = array();
	
	/**
	 * Check if a event exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function eventExists($eventId) {
		return SportsEvents::where('external_event_id', '=', $eventId) -> pluck('id');
	}
	

	public function getEvents($limit = 0, $cid = 0, $date = NULL) {

		//get the comp id if not set
		$compQuery = ($cid) ? ' AND ege.event_group_id = ' . $cid : '';

		//add limit if set
		$limitQuery = ($limit) ? ' LIMIT ' . $limit : '';

		$dateQuery = $this -> mkDateQuery($date, 'e.start_date');

		//get events/matches
		$query = ' SELECT e.id AS id, e.start_date AS event_start_time ';
		$query .= ', e.name AS event_name, e.event_id AS ext_event_id ';
		//$query.= ', ege.event_group_id AS compID, AS compName ';
		$query .= ' FROM tbdb_event AS e ';
		if ($cid) { $query .= ' INNER JOIN tbdb_event_group_event AS ege ON e.id = ege.event_id ';
		}
		$query .= $dateQuery;
		$query .= $compQuery;
		$query .= ' ORDER BY e.start_date ASC ';
		$query .= $limitQuery;

		$result = \DB::select($query);

		return $result;
	}

	private function mkDateQuery($date = NULL, $time_field) {
		if ($date && date('Y-m-d') != $date) {
			if (strtotime($date) < time()) {
				//date is in the past >> returns just on that date
				$dateQuery = ' WHERE ' . $time_field . ' LIKE "' . $date . '%" ';
			} else {
				//date is in the future >> returns from date to future
				$dateQuery = ' WHERE UNIX_TIMESTAMP(' . $time_field . ') > ' . strtotime($date);
			}
		} else {
			//no date or date is today >> returns from now to future
			$dateQuery = ' WHERE UNIX_TIMESTAMP(' . $time_field . ') > ' . time();
		}
		return $dateQuery;
	}

}
