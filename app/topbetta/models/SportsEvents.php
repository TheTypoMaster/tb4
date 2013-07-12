<?php
namespace TopBetta;

class SportsEvents extends \Eloquent {
	
	protected $table = 'tbdb_event_group_event';	
	
	protected $guarded = array();

	public static $rules = array();

	public function getEvents($limit = 0, $cid = 0, $date = NULL) {

		//get the comp id if not set
		$compQuery = ($cid != 0) ? ' AND ege.event_group_id = "' . $cid . '"' : false;
		
		if (!$compQuery) {
			return array();
		}

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

	public static function getNextEventsToJump($limit = 25) {
		
		$query = "select ege.event_group_id AS comp_id, eg.name AS comp_name, e.*, ts.name AS sport_name
		FROM tbdb_event AS e
		INNER JOIN tbdb_event_group_event AS ege ON e.id = ege.event_id
		INNER JOIN tbdb_event_group AS eg ON ege.event_group_id = eg.id
		INNER JOIN tbdb_tournament_sport AS ts ON ts.id = eg.sport_id 
		WHERE e.start_date > NOW()
		AND eg.type_code IS NULL
		ORDER BY e.start_date ASC
		LIMIT $limit";
		
		$result = \DB::select($query);

		return $result;				
		
	}

}
