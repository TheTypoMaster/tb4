<?php namespace TopBetta;

class BetSelection extends \Eloquent {
	
	protected $table = 'tbdb_bet_selection';
	
    protected $guarded = array();

    public static $rules = array();
	
	/**
	 * Runner for this bet selection
	 * 
	 * @return type
	 */
	public function selection() {
		return $this->belongsTo('TopBetta\RaceSelection', 'selection_id', 'id');
	}	
	
	public static function getExoticSelectionsForBetId($betId) {
		
		$selections = BetSelection::where('bet_id', '=', $betId)
				->leftJoin('tbdb_selection AS s', 's.id', '=', 'tbdb_bet_selection.selection_id')
				-> select('tbdb_bet_selection.position', 's.number')
				-> get();
		
		$selectionString = "";
		$count = 1;
		
		foreach ($selections as $selection) {
			
			if ($count != 1) {
				$selectionString .= ($selection -> position == $prevPosition) ? ',' : '/';
			}				
				
			$selectionString .= $selection -> number;
			
			$prevPosition = $selection -> position;
			$count++;
			
		}
		
		return $selectionString;
		
	}
	
}