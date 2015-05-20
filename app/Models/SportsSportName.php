<?php
namespace TopBetta\Models;

class SportsSportName extends \Eloquent {
    
	// protected $table = 'tbdb_sport_name';
	protected $table = 'tbdb_tournament_sport';
		
    protected $guarded = array();

    public static $rules = array();
    
    
    /**
     * Check if a sport exists.
     *
     * @return Integer
     * - The record ID if a record is found
     */
    static public function sportExists($sportName) {
    	return SportsSportName::where('name', '=', $sportName) -> pluck('id');
    }
        static public function getSportsNameByID($sportId) {
            return SportsSportName::where('id', '=', $sportId) -> pluck('name');
    }
}