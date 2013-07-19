<?php
namespace TopBetta;

class SportsOptions extends \Eloquent {
    protected $guarded = array();

    public static $rules = array();
	
	public function getOptions($eventId, $typeId) {

		return \DB::table('tbdb_market_type AS mt')
            ->join('tbdb_market AS m', 'mt.id', '=', 'm.market_type_id')
            ->join('tbdb_selection AS s', 'm.id', '=', 's.market_id')
            ->join('tbdb_selection_price AS sp', 's.id', '=', 'sp.selection_id')
            ->where('m.event_id', '=', $eventId)
            ->where('mt.id', '=', $typeId)                        
            ->select('s.name AS bet_selection', 'sp.win_odds AS odds', 's.bet_place_ref', 's.bet_type_ref', 's.external_selection_id', 's.id AS selection_id')->get();

	}	
}