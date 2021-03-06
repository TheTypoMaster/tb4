<?php
namespace TopBetta\Models;

class SportsOptions extends \Eloquent {

    protected $guarded = array();

    public static $rules = array();
	
	public function getOptions($eventId, $typeId) {

		$result =  \DB::table('tbdb_market_type AS mt')
            ->join('tbdb_market AS m', 'mt.id', '=', 'm.market_type_id')
            ->join('tbdb_selection AS s', 'm.id', '=', 's.market_id')
            ->join('tbdb_selection_price AS sp', 's.id', '=', 'sp.selection_id')
            ->where('m.event_id', '=', $eventId)
            ->where('m.id', '=', $typeId)
            ->where('s.selection_status_id', '=', '1')
            ->where('s.display_flag', '=', '1')
            ->where('sp.win_odds', '>', '1')
            ->select('m.market_status AS market_status','s.order AS order', 's.image_url AS image_url', 's.name AS bet_selection', 'sp.win_odds AS win_odds', 's.bet_place_ref', 's.bet_type_ref', 's.external_selection_id', 's.id AS selection_id', 'sp.line as line', 'm.id as type_id', 'm.event_id as event_id', 'sp.override_odds as override_odds', 'sp.override_type as override_type')->get();

        return array_filter($result, function($value) {
            return \App::make('TopBetta\Services\Betting\SelectionService')->calculatePrice($value->win_odds, $value->override_odds, $value->override_type) > 1;
        });
    }
	
	public function getOptionsForMarketType($allEvents, $marketTypeId) {
		$result = \DB::table('tbdb_market_type AS mt')
            ->join('tbdb_market AS m', 'mt.id', '=', 'm.market_type_id')
            ->join('tbdb_selection AS s', 'm.id', '=', 's.market_id')
            ->join('tbdb_selection_price AS sp', 's.id', '=', 'sp.selection_id')
            ->whereIn('m.event_id', $allEvents)
            ->where('mt.id', '=', $marketTypeId)
            ->where('s.selection_status_id', '=', '1')
            ->where('s.display_flag', '=', '1')
            ->where('sp.win_odds', '>', '1')
            ->select('m.market_status AS market_status', 's.order AS order', 's.image_url AS image_url', 's.name AS bet_selection', 'sp.win_odds AS win_odds', 's.bet_place_ref', 's.bet_type_ref', 's.external_selection_id', 's.id AS selection_id', 'sp.line as line', 'm.id as type_id', 'm.event_id as event_id', 'sp.override_odds as override_odds', 'sp.override_type as override_type')->get();

        return array_filter($result, function($value) {
            return \App::make('TopBetta\Services\Betting\Selectionservice')->calculatePrice($value->win_odds, $value->override_odds, $value->override_type) > 1;
        });
	}
}