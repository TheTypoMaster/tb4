<?php namespace TopBetta;

class SportsSelectionPrice extends \Eloquent {

	protected $table = 'tbdb_selection_price';
	
	/**
	 * Check if a selection price exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function selectionPriceExists($selectionId){
		return SportsSelectionPrice::where('selection_id', $selectionId)->pluck('id');
	}
	
}