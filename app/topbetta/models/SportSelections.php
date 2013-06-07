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
}