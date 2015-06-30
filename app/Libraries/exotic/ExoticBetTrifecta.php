<?php

namespace TopBetta\Libraries\exotic;

/**
 * Description of ExoticBetTrifecta
 *
 * @author mic
 */
class ExoticBetTrifecta extends ExoticBet implements ExoticBetInterface
{

	protected $positionSelectionCount = 3;

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
					foreach ($selectionList['third'] as $third) {
						if ($third !== $first && $third !== $second) {
							$combinationCount ++;
						}
					}
				}
			}
		}

		return $combinationCount;
	}

}
