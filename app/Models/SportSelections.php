<?php namespace TopBetta\Models;

class SportsSelection extends \Eloquent {

	protected $table = 'tbdb_selection';
	
	
	// Relationships
    public function markets()
    {
        return $this->belongsTo('TopBetta\Models\SportsMarket', 'market_id', 'id');
    }

    public function sportsResults()
	{
		return $this->hasOne('TopBetta\Models\SportsResults', 'selection_id', 'id');
	}

    public function selectionprice()
    {
        return $this->hasOne('TopBetta\Models\SportsSelectionPrice', 'selection_id', 'id');
    }

    public function selectionresult()
    {
        return $this->hasOne('TopBetta\Models\SportsSelectionResults', 'selection_id', 'id');
    }

    public function selectionstatus()
    {
        return $this->belongsTo('TopBetta\Models\SportsSelectionStatus', 'selection_status_id', 'id');
    }

	
	/**
	 * Check if a selection exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function selectionExists($selectionId){
		return SportsSelection::where('external_selection_id', $selectionId)->value('id');
	}
	
	static public function sportSelectionExists($selectionId, $marketId, $eventId){
		return SportsSelection::where('external_selection_id', $selectionId)
							->where('external_market_id', '=', $marketId)
							->where('external_event_id', '=', $eventId)
							->value('id');
	}
	
	static public function getWinningSelelctionID($eventId, $marketId, $score){
		return SportsSelection::where('external_event_id', $eventId)
								->where('external_market_id', '=', $marketId)
								->where('name', '=', $score)
								->value('id');
	}
	
	
}