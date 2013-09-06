<?php
namespace TopBetta;

class SportsComps extends \Eloquent {

	protected $table = 'tbdb_event_group';

    protected $guarded = array();

    public static $rules = array();


    /**
     * Check if a comp exists.
     *
     * @return Integer
     * - The record ID if a record is found
     */
    static public function compExists($compName) {
    	return SportsComps::where('name', '=', $compName) -> pluck('id');
    }

   public function getCompsSorted ($date = NULL, $sid = NULL){
    	//construct the date query string
    	$dateQuery = $this->mkDateQuery($date, 'eg.close_time');
    	
    	//select sports if ids set
    	$sportQuery = ($sid) ? ' AND s.id IN ('.$sid.') ' : '';
    	
    	$query = ' SELECT eg.name AS name, eg.created_date, eg.id AS eventGroupId, eg.start_date, eg.close_time';
    	$query .= ' FROM tbdb_event_group as eg';
    	$query .= ' LEFT JOIN tb_data_ordering_provider_match AS dopm ON dopm.provider_value = eg.name ';
    	$query .= ' LEFT JOIN tb_data_ordering_order AS doo ON doo.topbetta_keyword = dopm.topbetta_keyword';
    	$query .= ' LEFT JOIN tbdb_tournament_sport AS s ON s.id = eg.sport_id';
    	// $query .= ' LEFT JOIN tb_data_provider AS dp ON dp.id = dopm.provider_id ';
    	$query.= $dateQuery;
    	$query.= $sportQuery;
    	$query .= " AND eg.display_flag = '1'";
    	$query.= ' ORDER BY -doo.order_number DESC, eg.name ASC ';
		
    	$result = \DB::select($query);
    	
    	return $result;
     }

	public function getSportAndComps($date = NULL, $sid = NULL) {

			//construct the date query string
			$dateQuery = $this->mkDateQuery($date, 'c.close_time');

			//select sports if ids set
			//if ($sid) { $sportQuery = ' AND s.id IN ('.$sid.') ' ; }
			$sportQuery = ($sid) ? ' AND s.id IN ('.$sid.') ' : '';

			//get sports and competitions
			$query = ' SELECT s.id AS sportID, s.name AS sportName, c.name AS name, c.created_date, c.id AS eventGroupId ';
			$query.= ' , c.start_date, c.close_time ';
			$query.= ' FROM tbdb_tournament_sport AS s ';
			$query.= ' INNER JOIN tbdb_event_group AS c ON c.sport_id = s.id ';
			$query.= $dateQuery;
			$query.= $sportQuery;
			$query .= " AND c.display_flag = '1'";
			$query.= ' ORDER BY sportName, name ASC ';

			$result = \DB::select($query);

			return $result;
	}

	public function getCompWithSport($compId) {

		$query = ' SELECT s.id AS sportID, s.name AS sportName, c.name AS name, c.created_date, c.id AS eventGroupId ';
		$query.= ' , c.start_date, c.close_time ';
		$query.= ' FROM tbdb_tournament_sport AS s ';
		$query.= ' INNER JOIN tbdb_event_group AS c ON c.sport_id = s.id ';
		$query.= ' WHERE c.id = "' . $compId . '"';

		$result = \DB::select($query);

		return $result;

	}

	private function mkDateQuery($date = NULL, $time_field) {
		if ($date && date('Y-m-d') != $date) {
			if (strtotime($date) < time()) {
				//date is in the past >> returns just on that date
				$dateQuery = ' WHERE '.$time_field.' LIKE "'.$date.'%" ' ;
			} else {
				//date is in the future >> returns from date to future
				$dateQuery = ' WHERE UNIX_TIMESTAMP('.$time_field.') > '.strtotime($date) ;
			}
		} else {
			//no date or date is today >> returns from now to future
			$dateQuery = ' WHERE UNIX_TIMESTAMP('.$time_field.') > '.time() ;
		}
		return $dateQuery;
	}
}