<?php

jimport('mobileactive.wagering.bet.exotic');

class WageringBetExoticExacta extends WageringBetExotic implements iBetExotic{
	protected $position_selection_count = 2;
	protected $maximum_selection_count = 0;
	protected $flexi_bet_type = true;
	protected $combination_bet_type = true;
	protected $boxed_bet_type = true;
	
	public function getCombinationCount(){
		$combination_count = null;
		
		if($this->isBoxed()){
			$combination_count = $this->getBoxedCombinationCount();
			return $combination_count;
		}
		
		$selection = $this->getBetSelectionObject();
		$selection_list = $selection->getList();
		
		foreach($selection_list[1] as $first){
			foreach($selection_list[2] as $second){
				if($first !== $second){
				 	$combination_count ++;	
				}
			}
		}
		
		return $combination_count;
	}
}