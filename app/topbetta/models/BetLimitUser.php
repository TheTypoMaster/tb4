<?php

class BetLimitUser extends \Eloquent
{

	protected $table = 'tbdb_bet_limit_users';
	protected $fillable = array('user_id', 'bet_limit_type_id', 'amount', 'notes');
	public static $rules = array();

	/**
	 * Map Bet Limit Type for this Bet Limit User
	 * 
	 * @return type collection
	 */
	public function limitType()
	{
		return $this->belongsTo('BetLimitType', 'bet_limit_type_id');
	}

}
