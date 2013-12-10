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
            ->where('m.id', '=', $typeId)
            ->where('s.selection_status_id', '=', '1')                        
            ->select('s.name AS bet_selection', 'sp.win_odds AS odds', 's.bet_place_ref', 's.bet_type_ref', 's.external_selection_id', 's.id AS selection_id', 'sp.line as line')->get();

	}	
	
	public function getOptionsForMarketType($allEvents, $marketTypeId) {
		return \DB::table('tbdb_market_type AS mt')
            ->join('tbdb_market AS m', 'mt.id', '=', 'm.market_type_id')
            ->join('tbdb_selection AS s', 'm.id', '=', 's.market_id')
            ->join('tbdb_selection_price AS sp', 's.id', '=', 'sp.selection_id')
            ->whereIn('m.event_id', $allEvents)
            ->where('mt.id', '=', $marketTypeId)
            ->where('s.selection_status_id', '=', '1')                        
            ->select('s.name AS bet_selection', 'sp.win_odds AS odds', 's.bet_place_ref', 's.bet_type_ref', 's.external_selection_id', 's.id AS selection_id', 'sp.line as line')->get();		
	}
}