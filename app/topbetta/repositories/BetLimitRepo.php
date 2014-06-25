<?php

namespace TopBetta\Repositories;

use BetLimitType;
use BetLimitUser;
use TopBetta\Bet;
use TopBetta\BetTypes;
use TopBetta\Facades\BetRepo;
use TopBetta\RaceMeeting;

/**
 * Description of BetLimitRepo
 *
 * @author mic
 */
class BetLimitRepo
{

	/**
	 * @var UserRepo
	 */
	private $userRepo;
	private $betLimitType;
	private $betLimitUser;
	private $userBetLimits;

	public function __construct(BetLimitType $betLimitType, BetLimitUser $betLimitUser, UserRepo $userRepo)
	{
		$this->betLimitType = $betLimitType;
		$this->betLimitUser = $betLimitUser;
		$this->userRepo = $userRepo;
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
		$lowestFlexiLimit = false;

		// 1: check every rule the user has for a match
		foreach ($this->userBetLimits as $betLimit) {
			$limit = $lowestLimit;
			$flexiLimit = $lowestFlexiLimit;


			// ** NOTE: these case types need to match the database rule types (tbdb_bet_limit_types) **
			switch ($betLimit->limitType->name) {
				case 'bet_type':
					// win/place/exotic
					if ($betLimit->limitType->value == $betData['bet_type_id']) {
						$limit = $betLimit->amount * 100;
					}

					break;

				case 'bet_flexi':
					// exotics only
					if ($betLimit->limitType->value == $betData['bet_type_id']) {
						// as percentage
						$flexiLimit = $betLimit->amount;
					}

					break;

				case 'meeting_type':
					// R,G,H
					if ($betLimit->limitType->value == $meeting->type_code) {
						$limit = $betLimit->amount * 100;
					}
					break;

				default:
					break;
			}

			if (!$lowestLimit || $limit < $lowestLimit) {
				$lowestLimit = $limit;
			}

			if (!$lowestFlexiLimit || $flexiLimit < $lowestFlexiLimit) {
				$lowestFlexiLimit = $flexiLimit;
			}
		}

		// 2: if we didn't find a user rule matching, fetch global default limits
		if (!$lowestLimit) {
			$lowestLimit = $this->getDefaultBetLimit();
		}

		if (!$lowestFlexiLimit) {
			$lowestFlexiLimit = $this->getDefaultBetFlexiLimit();
		}

		// 3: do our checks now
		if (BetTypes::find($betData['bet_type_id'])->isExotic()) {
			// exotic bet
			$exoticClass = "\\TopBetta\\libraries\\exotic\\ExoticBet" . ucfirst(BetTypes::where('id', $betData['bet_type_id'])->pluck('name'));
			$exotic = new $exoticClass;
			$exotic->selections = $betData['selection'];
			$exotic->betAmount = $betData['value'];

			$newExoticTotal = $betData['value'];
			$newExoticPercent = $exotic->getFlexiPercentage();

			// look for any exotic bets of the same type for this event and user
			$previousExoticBets = Bet::with('selections')
					->where('event_id', $betData['race_id'])
					->where('bet_type_id', $betData['bet_type_id'])
					->where('user_id', \Auth::user()->id)
					->get();

			// check if any existing exotic bets match the current bet
			foreach ($previousExoticBets as $bet) {
				if (BetRepo::checkSelectionsMatchExoticBet($betData['selection'], $bet)) {
					// identical exotic bet
					$newExoticTotal += $bet->amount + $bet->bet_freebet_amount;
					$newExoticPercent += $bet->percentage;
				}
			}

			// does this exceed any matched limits
			$flexiExceeds = $newExoticPercent > $lowestFlexiLimit;
			$betValueExceeds = $lowestLimit && $newExoticTotal > $lowestLimit;

			if ($flexiExceeds || $betValueExceeds) {
				return array(
					'result' => true,
					'flexiExceeds' => $flexiExceeds,
					'flexiLimit' => $lowestFlexiLimit,
					'betValueExceeds' => $betValueExceeds,
					'betValueLimit' => number_format($lowestLimit / 100, 2)
				);
			}

			return array('result' => false);
		} else {
			$previousTotal = $this->userRepo
					->sumUserBetsForSelectionAndType($betData['selection'], $betData['bet_type_id'], \Auth::user()->id);

			$newTotal = (int) $previousTotal + (int) $betData['value'];

//			return ($lowestLimit && $newTotal > $lowestLimit) ? true : false;
			if ($lowestLimit && $newTotal > $lowestLimit) {
				return array(
					'result' => true,
					'betValueLimit' => number_format($lowestLimit / 100, 2)
				);
			}

			return array('result' => false);			
		}
	}

	/**
	 * Get all bet limits for a user
	 * 
	 * @param int $userId
	 * @return collection
	 */
	public function getBetLimitsForUser($userId, $paginate = false)
	{
		$query = $this->betLimitUser
				->where('user_id', $userId)
				->orderBy('amount', 'asc')
				->with('limitType');

		return ($paginate) ? $query->paginate() : $query->get();
	}

	public function getUserBetLimitWithId($id)
	{
		return $this->betLimitUser->find($id);
	}

	public function getAllLimitTypesNicknames($excludeGlobal = true)
	{
		$list = $this->betLimitType->lists('nickname', 'id');

		return ($excludeGlobal) ?
				array_except($list, array('1', '2')) :
				$list;
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
	 * Get the default global bet flexi limit
	 * 
	 * @return int
	 */
	public function getDefaultBetFlexiLimit()
	{
		return $this->betLimitType
						->where('name', 'default_flexi')
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
