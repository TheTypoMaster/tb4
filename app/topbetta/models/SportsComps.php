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
    
    	
	public function getSportAndComps($date = NULL, $sid = NULL) {

			//construct the date query string
			$dateQuery = $this->mkDateQuery($date, 'c.close_time');
			
			//select sports if ids set
			//if ($sid) { $sportQuery = ' AND s.id IN ('.$sid.') ' ; }
			$sportQuery = ($sid) ? ' AND s.id IN ('.$sid.') ' : '';

			//get sports and competitions
			$query = ' SELECT s.id AS sportID, sportName, name, c.created_date, c.id AS eventGroupId ';
			$query.= ' , c.start_date, c.close_time ';
			$query.= ' FROM tbdb_sport_name AS s ';
			$query.= ' INNER JOIN tbdb_event_group AS c ON c.sport_id = s.id ';
			$query.= $dateQuery;
			$query.= $sportQuery;
			$query.= ' ORDER BY sportName, name ASC ';

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