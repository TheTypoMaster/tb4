<?php
namespace TopBetta;

use TopBetta\Services\Betting\SelectionService;

class SportsComps extends \Eloquent {

	protected $table = 'tbdb_event_group';

    protected $guarded = array();

    public static $rules = array();

    public function sports(){
        return $this->belongsTo('\TopBetta\TournamentSport', 'sport_id', 'id');
    }

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

       //event date query
       $eventDateQuery = $this->mkDateQuery($date, 'ev.start_date', "AND");
    	
    	//select sports if ids set
    	$sportQuery = ($sid) ? ' AND s.id IN ('.$sid.') ' : '';
    	
    	$query = ' SELECT eg.name AS name, eg.created_date, eg.id AS eventGroupId, eg.start_date, eg.close_time, sp.win_odds, sp.override_odds, sp.override_type';
    	$query .= ' FROM tbdb_event_group as eg';
	    $query .= ' INNER JOIN tbdb_event_group_event AS ege ON ege.event_group_id = eg.id';
        $query.= ' INNER JOIN tbdb_event AS ev ON ege.event_id = ev.id ';
	    $query .= ' INNER JOIN tbdb_market AS m ON m.event_id = ege.event_id';
	   	$query.= ' INNER JOIN tbdb_selection sel ON sel.market_id = m.id';
	   	$query.= ' INNER JOIN tbdb_selection_price sp ON sel.id = sp.selection_id';
    	$query .= ' LEFT JOIN tb_data_ordering_provider_match AS dopm ON dopm.provider_value = eg.name ';
    	$query .= ' LEFT JOIN tb_data_ordering_order AS doo ON doo.topbetta_keyword = dopm.topbetta_keyword';
    	$query .= ' LEFT JOIN tbdb_tournament_sport AS s ON s.id = eg.sport_id';
    	// $query .= ' LEFT JOIN tb_data_provider AS dp ON dp.id = dopm.provider_id ';
    	$query.= $dateQuery;
    	$query.= $sportQuery;
        $query.= $eventDateQuery;
    	$query .= " AND eg.display_flag = '1'";
        $query .= " AND ev.display_flag = '1' ";
        $query .= " AND m.display_flag = '1' ";
        $query .= " AND sel.display_flag = '1' ";
	   	$query .= " AND m.market_status NOT IN ('D', 'S') ";
	    $query .= " AND ((sp.win_odds > 1 AND sp.override_type IS NULL) OR (sp.override_odds > 1 AND sp.override_type = 'price') OR (sp.override_odds * sp.win_odds > 1 AND sp.override_type='percentage'))";
	    $query .= " AND sel.selection_status_id = '1'";
	   	$query .= ' GROUP BY eventGroupId';
    	$query.= ' ORDER BY -doo.order_number DESC, eg.name ASC ';
		
    	$result = \DB::select($query);


       return array_filter($result, function($value) {
           return \App::make('TopBetta\Services\Betting\SelectionService')->calculatePrice($value->win_odds, $value->override_odds, $value->override_type) > 1;
       });
     }

	public function getSportAndComps($date = NULL, $sid = NULL) {

			//construct the date query string
			$dateQuery = $this->mkDateQuery($date, 'c.close_time');

            //event date query
            $eventDateQuery = $this->mkDateQuery($date, 'ev.start_date', "AND");

			//select sports if ids set
			//if ($sid) { $sportQuery = ' AND s.id IN ('.$sid.') ' ; }
			$sportQuery = ($sid) ? ' AND s.id IN ('.$sid.') ' : '';

			//get sports and competitions
			$query = ' SELECT s.id AS sportID, s.name AS sportName, c.name AS name, c.created_date, c.id AS eventGroupId, sp.win_odds, sp.override_odds, sp.override_type ';
			$query.= ' , c.start_date, c.close_time ';
			$query.= ' FROM tbdb_tournament_sport AS s ';
			$query.= ' INNER JOIN tbdb_event_group AS c ON c.sport_id = s.id ';
			$query.= ' INNER JOIN tbdb_event_group_event AS ege ON ege.event_group_id = c.id';
            $query.= ' INNER JOIN tbdb_event AS ev ON ege.event_id = ev.id ';
			$query.= ' INNER JOIN tbdb_market AS m ON m.event_id = ege.event_id';
			$query.= ' INNER JOIN tbdb_selection sel ON sel.market_id = m.id';
			$query.= ' INNER JOIN tbdb_selection_price sp ON sel.id = sp.selection_id';
			$query.= $dateQuery;
			$query.= $sportQuery;
            $query.= $eventDateQuery;
			$query .= " AND c.display_flag = '1' ";
            $query .= " AND ev.display_flag = '1' ";
            $query .= " AND m.display_flag = '1' ";
            $query .= " AND sel.display_flag = '1' ";
			$query .= " AND m.market_status NOT IN ('D', 'S') ";
			$query .= " AND ((sp.win_odds > 1 AND sp.override_type IS NULL) OR (sp.override_odds > 1 AND sp.override_type = 'price') OR (sp.override_odds * sp.win_odds > 1 AND sp.override_type='percentage'))";
			$query .= " AND sel.selection_status_id = '1'";
			$query.= " GROUP BY sportId, eventGroupId";
			$query.= ' ORDER BY sportName, name ASC ';

			//dd($query);
			$result = \DB::select($query);

        return array_filter($result, function($value) {
            return \App::make('TopBetta\Services\Betting\SelectionService')->calculatePrice($value->win_odds, $value->override_odds, $value->override_type) > 1;
        });
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

	private function mkDateQuery($date = NULL, $time_field, $predicate = 'WHERE') {
		if ($date && date('Y-m-d') != $date) {
			if (strtotime($date) < time()) {
				//date is in the past >> returns just on that date
				$dateQuery = " $predicate ".$time_field.' LIKE "'.$date.'%" ' ;
			} else {
				//date is in the future >> returns from date to future
				$dateQuery = " $predicate UNIX_TIMESTAMP(".$time_field.") > ".strtotime($date) ;
			}
		} else {
			//no date or date is today >> returns from now to future
			$dateQuery = " $predicate UNIX_TIMESTAMP(".$time_field.") > ".time() ;
		}
		return $dateQuery;
	}
}