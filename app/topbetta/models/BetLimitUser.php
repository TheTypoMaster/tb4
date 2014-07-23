<?php

class BetLimitUser extends \Eloquent
{

	protected $table = 'tbdb_bet_limit_users';
	protected $fillable = array('user_id', 'bet_limit_type_id', 'amount', 'notes');
	public static $rules = array(
		'bet_limit_type_id' => 'required',
		'amount' => 'required'
	);

	/**
	 * Map Bet Limit Type for this Bet Limit User
	 * 
	 * @return type collection
	 */
	public function limitType()
	{
		return $this->belongsTo('BetLimitType', 'bet_limit_type_id');
	}
	
	/**
	 * Simplifies delaing with display of limit amount
	 * 
	 * @param type $value
	 * @return type
	 */
	public function getAmountAttribute($value) {
		return $value / 100;
	}
	
	/**
	 * Simplifies handling the saving of limit amount
	 * 
	 * @param type $value
	 */
	public function setAmountAttribute($value) {
		$this->attributes['amount'] = $value * 100;
	}	

}
