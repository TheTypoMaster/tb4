<?php
namespace TopBetta;

class SportsSportName extends \Eloquent {
    
	protected $table = 'tbdb_sport_name';	
		
    protected $guarded = array();

    public static $rules = array();
    
    
    /**
     * Check if a sport exists.
     *
     * @return Integer
     * - The record ID if a record is found
     */
    static public function sportExists($sportName) {
    	return SportsSportName::where('sportName', '=', $sportName) -> pluck('id');
    }
}