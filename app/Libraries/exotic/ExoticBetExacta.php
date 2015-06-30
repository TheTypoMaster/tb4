<?php

namespace TopBetta\Libraries\exotic;

/**
 * Description of ExoticBetExacta
 *
 * @author mic
 */
class ExoticBetExacta extends ExoticBet implements ExoticBetInterface
{

	protected $positionSelectionCount = 2;

	public function getCombinationCount()
	{
		$combinationCount = 0;

		if ($this->isBoxed()) {
			$combinationCount = $this->getBoxedCombinationCount();
			return $combinationCount;
		}

		$selectionList = $this->selections;

		foreach ($selectionList['first'] as $first) {
			foreach ($selectionList['second'] as $second) {
				if ($first !== $second) {
					$combinationCount ++;
				}
			}
		}

		return $combinationCount;
	}

}
