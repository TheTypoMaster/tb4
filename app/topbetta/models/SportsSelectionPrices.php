<?php namespace TopBetta;

class SportsSelectionPrice extends \Eloquent {

	protected $table = 'tbdb_selection_price';

    public function selections()
    {
        return $this->belongsTo('TopBetta\SportsSelection', 'selection_id', 'id')->whereNull('number');
    }
	
	/**
	 * Check if a selection price exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function selectionPriceExists($selectionId){
		return SportsSelectionPrice::where('selection_id', '=', $selectionId)->pluck('id');
	}
	
}