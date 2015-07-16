<?php

class WageringBetValidator 
{
	private $bet = null;
	
	const 
		BET_ERROR_INVALID_COMBINATION = 1,
		BET_ERROR_AMOUNT_TOO_LOW = 2,
		BET_ERROR_MINIMUM_UNIQUE_SELECTION = 3,
		BET_ERROR_TOO_MANY_COMBINATIONS = 4,
		BET_ERROR_INVALID_RACE_POOL = 5;
	
	static $error_message_list = array(
		self::BET_ERROR_AMOUNT_TOO_LOW => 'Bet value must be greater than 50 cents',
		self::BET_ERROR_INVALID_COMBINATION => 'Invalid combination selected',
		self::BET_ERROR_MINIMUM_UNIQUE_SELECTION => 'You have not made enough selections for this bet type',
		self::BET_ERROR_TOO_MANY_COMBINATIONS => 'You have too many combinations',
		self::BET_ERROR_INVALID_RACE_POOL => 'There has been a problem placing your bet (Invalid Race Pool Id)'
	);
	
	public function __construct(WageringBet $bet)
	{
		$this->bet = $bet;

		if (!$this->_isBetValueValid()) {
			throw new ValidatorException(self::BET_ERROR_AMOUNT_TOO_LOW);
		}

		if (!$this->_isCombinationValid()) {
			throw new ValidatorException(self::BET_ERROR_INVALID_COMBINATION);
		}
		
		/*if (!$this->_isRacePoolIdValid()) {
			throw new ValidatorException(self::BET_ERROR_INVALID_RACE_POOL);
		}*/
		
		if (!$this->_isEnoughSelections()) {
			throw new ValidatorException(self::BET_ERROR_MINIMUM_UNIQUE_SELECTION);
		}
		
		if (!$this->_isTooManySelections()) {
			throw new ValidatorException(self::BET_ERROR_TOO_MANY_COMBINATIONS);
		}
	}
	
	private function _isBetValueValid()
	{
		return ($this->bet->amount >= 50);
	}
	
	private function _isCombinationValid()
	{
		if ($this->bet->isCombinationBetType()) {
			return ($this->bet->getCombinationCount() != 0);
		} else {
			return true;
		}
	}
	
	private function _isEnoughSelections(){
		$selection_count = $this->bet->getBetSelectionObject()->count();
		$position_selection_count = $this->bet->getPositionSelectionCount();
		return ($selection_count >= $position_selection_count);
	}
	
	private function _isTooManySelections(){
		$selection_count = $this->bet->getBetSelectionObject()->count();
		$position_selection_count =  $this->bet->getPositionSelectionCount();
		// FIX ME: need to check with tastab
		$maximum_selection_count = 0;
		if($position_selection_count == 0){
			return true;
		}
		return ($selection_count > $maximum_selection_count );
	}
	
	private function _isRacePoolIdValid(){
		$bet_type_array = array('win' => 'W',
			'place' => 'P',
			'quinella' => 'Q',
			'exacta' => 'E',
			'trifecta' => 'T',
			'firstfour' => 'FF',
			'quadrella' => 'QD'
		);
		
		$bet_type_name = strtolower($this->bet->getBetType());
		
		$bet_type = $bet_type_array[$bet_type_name];
		
		return ($this->bet->race_number[$bet_type] > 0);
	}
}

class ValidatorException extends Exception
{
	public function __construct($error_code)
	{
		$error_message = WageringBetValidator::$error_message_list[$error_code];
		parent::__construct($error_message);
	}
}
