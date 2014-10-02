<?php namespace TopBetta;

class SportsMarket extends \Eloquent {

	protected $table = 'tbdb_market';


    public function markettypes(){
        return $this->belongsTo('\TopBetta\SportsMarketType', 'market_type_id', 'id');
    }

    public function events(){
        return $this->belongsTo('\TopBetta\Models\Events', 'event_id', 'id');
    }

	/**
	 * Check if a meeting exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function marketExists($marketID){
		return SportsMarket::where('external_market_id', '=', $marketID )->pluck('id');
	}
	
	static public function sportMarketExists($marketID, $eventID){
		return SportsMarket::where('external_market_id', '=', $marketID )
							->where('external_event_id','=',$eventID)
							->pluck('id');
	}

}