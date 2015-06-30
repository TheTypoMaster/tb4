<?php

namespace TopBetta\Libraries\exotic;

/**
 * Description of ExoticBetQuinella
 *
 * @author mic
 */
class ExoticBetQuinella extends ExoticBet implements ExoticBetInterface
{

	protected $positionSelectionCount = 2;

	public function getCombinationCount()
	{
		$combinationCount = 0;

		$selectionCount = $this->countSelections();
		$selectionCountDifference = $selectionCount - $this->positionSelectionCount;

		if ($selectionCountDifference === 0) {
			/* reference http://www.rwwa.com.au/home/quinella.html */
			return $selectionCount * ($selectionCount - 1) / 2;
		} elseif ($selectionCountDifference < 0) {
			return 0;
		} else {
			return $this->factorial($selectionCount) / ($this->factorial($this->positionSelectionCount) * $this->factorial($selectionCountDifference));
		}

		return $combinationCount;
	}

}
