<?php

namespace TopBetta\Libraries\exotic;

/**
 * Description of ExoticBetFirstfour
 *
 * @author mic
 */
class ExoticBetFirstfour extends ExoticBet implements ExoticBetInterface
{

	protected $positionSelectionCount = 4;

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
							foreach ($selectionList['fourth'] as $fourth) {
								if ($fourth !== $first && $fourth !== $second && $fourth !== $third ) {						
									$combinationCount ++;
								}
							}
						}
					}
				}
			}
		}

		return $combinationCount;
	}

}
