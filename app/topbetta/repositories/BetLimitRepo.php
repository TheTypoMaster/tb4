<?php

namespace TopBetta\Repositories;

use BetLimitType;
use BetLimitUser;
use TopBetta\RaceMeeting;

/**
 * Description of BetLimitRepo
 *
 * @author mic
 */
class BetLimitRepo
{

	private $betLimitType;
	private $betLimitUser;
	private $userBetLimits;

	public function __construct(BetLimitType $betLimitType, BetLimitUser $betLimitUser)
	{
		$this->betLimitType = $betLimitType;
		$this->betLimitUser = $betLimitUser;
	}

	/**
	 * Determine the bet limit for this bet based on the user and defaults
	 * 
	 * @param array $betData
	 * @param string $source // racing/sports
	 * @return boolean
	 */
	public function checkExceedBetLimitForBetData(array $betData, $source)
	{
		$this->userBetLimits = $this->getBetLimitsForUser(\Auth::user()->id);

		if ($source == 'racing') {
			return $this->checkExceedRacingLimits($betData);
		} else {
			return false;
		}
	}

	private function checkExceedRacingLimits($betData)
	{
		$meeting = RaceMeeting::find($betData['id']);

		$lowestLimit = false;

		// 1: check every rule the user has for a match
		foreach ($this->userBetLimits as $betLimit) {
			$limit = $lowestLimit;
			
			// ** NOTE: these case types need to match the database rule types (tbdb_bet_limit_types) **
			switch ($betLimit->limitType->name) {
				case 'bet_type':
					// win/place/exotic
					if ($betLimit->limitType->value == $betData['bet_type_id']) {
						$limit = $betLimit->amount;
					}

					break;

				case 'meeting_type':
					// R,G,H
					if ($betLimit->limitType->value == $meeting->type_code) {
						$limit = $betLimit->amount;
					}
					break;

				default:
					break;
			}

			if (!$lowestLimit || $limit < $lowestLimit) {
				$lowestLimit = $limit;
			}
		}

		// 2: if we didn't find a user rule matching, check against global default limit
		if (!$lowestLimit) {
			$lowestLimit = $this->getDefaultBetLimit();
		}

		return ($lowestLimit && $betData['value'] > $lowestLimit) ? true : false;
	}

	/**
	 * Get all bet limits for a user
	 * 
	 * @param int $userId
	 * @return collection
	 */
	public function getBetLimitsForUser($userId)
	{
		return $this->betLimitUser
						->where('user_id', $userId)
						->orderBy('amount', 'asc')
						->with('limitType')
						->get();
	}

	/**
	 * Get the default global bet limit
	 * 
	 * @return int
	 */
	public function getDefaultBetLimit()
	{
		return $this->betLimitType
						->where('name', 'default')
						->pluck('default_amount');
	}

	/**
	 * Find the bet limit for a type with a value
	 * Fall back to default global bet limit
	 * 
	 * @param string $type
	 * @param string $value
	 * @return int
	 */
	public function getBetLimitForTypeAndValue($type, $value)
	{
		$limit = $this->betLimitType
				->where('name', $type)
				->where('value', $value)
				->pluck('default_amount');

		if (!$limit) {
			$limit = $this->getDefaultBetLimit();
		}

		return $limit;
	}

}
