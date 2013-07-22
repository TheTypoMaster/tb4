<?php namespace TopBetta;

class SportsSelection extends \Eloquent {

	protected $table = 'tbdb_selection';
	
	/**
	 * Check if a selection exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function selectionExists($selectionId){
		return SportsSelection::where('external_selection_id', $selectionId)->pluck('id');
	}
	
	static public function sportSelectionExists($selectionId, $marketId, $eventId){
		return SportsSelection::where('external_selection_id', $selectionId)
							->where('external_market_id', '=', $marketId)
							->where('external_event_id', '=', $eventId)
							->pluck('id');
	}
	
}