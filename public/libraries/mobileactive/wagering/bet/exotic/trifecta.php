<?php
defined('_JEXEC') or die();

jimport('mobileactive.wagering.bet.exotic');

/**
 * Exotic trifecta specific class
 * 
 * @author 		Geoff Wellman
 * @package 	mobileactive
 * @subpackage 	wagering
 * @since 		3.0
 */
class WageringBetExoticTrifecta extends WageringBetExotic implements iBetExotic
{
	/**
	 * number of positions that need to be filled
	 * 
	 * @var int
	 */
	protected $position_selection_count = 3;
	/**
	 * Maximum selection count ( 0 = unlimited )
	 * 
	 * @var int
	 */
	protected $maximum_selection_count = 0;
	/**
	 * Flexi betting allowed for this bet type
	 *
	 * @var boolean
	 */
	protected $flexi_bet_type = true;
	/**
	 * Combinations allowed for this bet type
	 *
	 * @var boolean
	 */
	protected $combination_bet_type = true;
	/**
	 * Boxed allowed for this bet type
	 *
	 * @var boolean
	 */
	protected $boxed_bet_type = true;
	/**
	 * Get the possible combinations count from current selections
	 * 
	 * @see iBetExotic::getCombinationCount()
	 * @return int
	 */
	public function getCombinationCount()
	{
		$combination_count = 0;
			
		if($this->isBoxed()){
			$combination_count = $this->getBoxedCombinationCount();
			return $combination_count;
		}
		
		$selection = $this->getBetSelectionObject();
		$selection_list = $selection->getList();
		
		foreach($selection_list[1] as $first){
			foreach($selection_list[2] as $second){
				if($first !== $second){
					foreach($selection_list[3] as $third){
						if($third !== $first && $third !== $second ){	 					
							$combination_count ++;
						}
					}		
				}
			}
		}
		
		return $combination_count;
	}
}
