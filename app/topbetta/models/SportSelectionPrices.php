<?php namespace TopBetta;

class SportSelectionPrice extends \Eloquent {

	protected $table = 'tbdb_selection_price';
	
	/**
	 * Check if a selection price exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function selectionpPriceExists($selectionId){
		return SportSelectionPrice::where('external_selection_id', $selectionId)->pluck('id');
	}
	
}