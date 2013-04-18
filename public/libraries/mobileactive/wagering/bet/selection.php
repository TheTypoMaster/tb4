<?php

class WageringBetSelection
{
	
	private $bet_selection_list = array();
	private $position_selection = null;
	private $bet = null;
	
	public function __construct(WageringBet $bet)
	{
		$this->bet = $bet;
	}
	
	public function add($number, $position = null)
	{
		if (is_null($position)) {
			if($this->position_selection === true){
				throw new Exception('You have already started a position selection, you must continue to specify positions');
			}
			$this->bet_selection_list[] = $number;
			$this->position_selection = false;
		} else {
			if ($this->position_selection === false) {
				throw new Exception('You have already started a non position selection, position must remain NULL');
			}
			if ($this->bet->isBoxed()) {
				throw new Exception('You can not select a position on a boxed bet');
			}
			if (is_null($this->position_selection)) {
				$this->bet_selection_list = array_fill(1, $this->bet->getPositionSelectionCount(), array());
			}
			$this->bet_selection_list[(int) $position][] = $number;
			$this->position_selection = true;
		}
	}
	
	public function __toString()
	{
		$bet_selection_list = $this->bet_selection_list;
		ksort($bet_selection_list, SORT_NUMERIC);
		
		if ($this->position_selection === true) {
			foreach ($bet_selection_list as $position => $selection) {
				if (is_array($selection)) {
					$bet_selection_list[$position] = implode('+', $selection);
				}
			}
		}
		return implode($this->position_selection ? '/' : '+', $bet_selection_list);
	}
	
	public function count()
	{
		if (is_null($this->position_selection)) {
			throw new Exception('No selections to count');
		}
		
		if ($this->position_selection === true) {
			$count = 0;
			foreach ($this->bet_selection_list as $selection_position) {
				$count += count($selection_position);
			}
			return $count;	
		}
		
		return count($this->bet_selection_list);
	}
	
	public function getList()
	{
		return $this->bet_selection_list;	
	}
}