<?php

jimport('mobileactive.wagering.bet.exotic');

class WageringBetExoticQuinella extends WageringBetExotic implements iBetExotic{
	protected $position_selection_count = 2;
	protected $maximum_selection_count = 0;
	protected $flexi_bet_type = false;
	protected $combination_bet_type = true;
	protected $boxed_bet_type = false;
	
	public function getCombinationCount(){
		$combination_count = 0;
		
		$selection = $this->getBetSelectionObject();
		$selection_count = $selection->count();
		$selection_count_difference = (int) $selection_count - $this->position_selection_count;
		
		if ($selection_count_difference === 0){
			//return $this->factorial($selection_count);
			/*redmine issue #9805   reference http://www.rwwa.com.au/home/quinella.html */
			return $selection_count * ($selection_count - 1) / 2;
		}
		elseif ($selection_count_difference < 0){
			return 0;
		}
		else{
			return $this->factorial($selection_count) / ($this->factorial($this->position_selection_count) * $this->factorial($selection_count_difference));
		}
		
		return $combination_count;
	}
}