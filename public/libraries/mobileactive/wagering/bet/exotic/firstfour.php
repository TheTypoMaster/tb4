<?php

jimport('mobileactive.wagering.bet.exotic');

/**
 * Exotic firstfour specific class
 * 
 * @author 		Geoff Wellman
 * @package 	mobileactive
 * @subpackage 	wagering
 * @since 		3.0
 */

class WageringBetExoticFirstfour extends WageringBetExotic implements iBetExotic{
	protected $position_selection_count = 4;
	protected $maximum_selection_count = 0;
	protected $flexi_bet_type = true;
	protected $combination_bet_type = true;
	protected $boxed_bet_type = true;
	
	public function getCombinationCount()
	{
		$combination_count = 0;
		
		if ($this->isBoxed()) {
				$combination_count = $this->getBoxedCombinationCount();
				return $combination_count;
		}
		
		$selection = $this->getBetSelectionObject();
		$selection_list = $selection->getList();
	
		foreach ($selection_list[1] as $first) {
			foreach ($selection_list[2] as $second) {
				if ($first !== $second) {
					foreach ($selection_list[3] as $third) {
						if ($third !== $first && $third !== $second ) {				
							foreach ($selection_list[4] as $fourth) {
								if ($fourth !== $first && $fourth !== $second && $fourth !== $third ) {						
									$combination_count ++;
								}
							}
						}
					}		
				}
			}
		}
		
		return $combination_count;
	}
}