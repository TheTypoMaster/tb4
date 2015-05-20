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

	//quick function for getting excluded bet types from international events

	public static function getExcludedBetTypesForInternationalRaces()
	{
		$betTypes = BetTypes::where("id", ">", 3)->get();

		return array_map(function($betType){
			if($betType['name'] == 'firstfour') {
				return "firstFour";
			}

			return $betType['name'];
		}, $betTypes->toArray());
	}

}
