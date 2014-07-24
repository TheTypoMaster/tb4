<?php

namespace TopBetta;

class BetTypes extends \Eloquent
{

	protected $table = 'tbdb_bet_type';
	protected $guarded = array();
	public static $rules = array();

	public function isExotic()
	{
		return ($this->id < 4) ? false : true;
	}

}
