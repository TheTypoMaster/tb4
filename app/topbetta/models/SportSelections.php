<?php namespace TopBetta;

class SportSelection extends \Eloquent {

	protected $table = 'tbdb_selection';
	
	/**
	 * Check if a selection exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function selectionExists($selectionId){
		return \DB::table('tbdb_selection')->where('external_selection_id', $selectionId)->pluck('id');
	}
}