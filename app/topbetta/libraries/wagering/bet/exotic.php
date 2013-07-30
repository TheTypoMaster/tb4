<?php 

interface iBetExotic{
	public function getCombinationCount();	
}

abstract class WageringBetExotic extends WageringBet implements iBetExotic{
	
	protected function getBoxedCombinationCount(){
		$selection = $this->getBetSelectionObject();
		$selection_count = $selection->count();
		$selection_count_difference = (int) $selection_count - $this->position_selection_count;
		
		if ($selection_count_difference === 0){
			return $this->factorial($selection_count);
		}
		elseif ($selection_count_difference < 0){
			return 0;
		}
		else{
			return $this->factorial($selection_count) / $this->factorial($selection_count_difference);
		}
	}
	
	protected function factorial($n){
		$factorial = $n;
		
		for($i=$n-1; $i > 1; $i--){
			$factorial *= $i;
		}
	
		return $factorial;
	}
	
	public function getFlexiPercentage() {
		$flexi = null;
		if ($this->isFlexiBet()) {
			$bet_amount 		= $this->getBetAmount();
			$combination_count	= 1;
			
			if ($this->isCombinationBetType()) {
				$combination_count = $this->getCombinationCount();
			}
			
			if ($bet_amount >0 && $combination_count > 0) {
				$flexi = bcdiv($bet_amount,$combination_count,2);
			}
		}
		
		return $flexi;
	}
}