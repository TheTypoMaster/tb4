<?php namespace TopBetta\Models;

class SportsSelectionResults extends \Eloquent {
    protected $guarded = array();

    public static $rules = array();
    
    protected $table = 'tbdb_selection_result';
    
    /**
     * Check if a selection result exists.
     *
     * @return Integer
     * - The record ID if a record is found
     */
    static public function selectionResultExists($selectionId){
    	return SportsSelectionResults::where('selection_id', $selectionId)->value('id');
    }
    
    
}