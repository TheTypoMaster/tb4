<?php namespace TopBetta\Models;

class BetLimitType extends \Eloquent
{

	protected $table = 'tbdb_bet_limit_types';
	protected $fillable = array('name', 'value', 'default_amount', 'notes');
	public static $rules = array();

	/**
	 * Map Bet Limit Users for this bet limit type
	 * 
	 * @return type collection
	 */
	public function users() {
		return $this->hasMany('BetLimitUser', 'bet_limit_type_id');
	}
}
