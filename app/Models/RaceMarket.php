<?php namespace TopBetta\Models;

class RaceMarket extends \Eloquent {

	protected $table = 'tbdb_market';
    protected $guarded = array();
    public static $rules = array();

    /**
     * @return mixed
     */
    public function event(){
        return $this->belongsTo('TopBetta\RaceEvent', 'event_id', 'id');
    }

    /**
     * @return mixed
     */
    public function markettype(){
        return $this->hasOne('TopBetta\Models\RaceMarkettType', 'id', 'market_type_id');
    }

	
	/**
	 * Check if a meeting exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	 public function marketExists($eventID, $marketTypeID){
		return $this->where('event_id', '=', $eventID)
							->where('market_type_id', '=', $marketTypeID)->pluck('id');
	}
}