<?php

namespace TopBetta\Libraries\exotic;

/**
 * Description of ExoticBet
 *
 * @author mic
 */
abstract class ExoticBet
{

	private $isFlexiBet = true;
	public $selections;
	public $betAmount;

    protected $positionSelectionCount;

	public function getFlexiPercentage()
	{
		$flexi = null;
		if ($this->isFlexiBet) {
			$combinationCount = $this->getCombinationCount();

			if ($this->betAmount > 0 && $combinationCount > 0) {
				$flexi = bcdiv($this->betAmount, $combinationCount, 2);
			}
		}

		return $flexi;
	}

	protected function getBoxedCombinationCount()
	{
		$selectionCount = $this->countSelections();
		$selectionCountDifference = $selectionCount - $this->positionSelectionCount;

		if ($selectionCountDifference === 0) {
			return $this->factorial($selectionCount);
		} elseif ($selectionCountDifference < 0) {
			return 0;
		} else {
			return $this->factorial($selectionCount) / $this->factorial($selectionCountDifference);
		}
	}

	protected function factorial($n)
	{
		$factorial = $n;

		for ($i = $n - 1; $i > 1; $i--) {
			$factorial *= $i;
		}

		return $factorial;
	}

	/**
	 * Boxed = all selections in "first" position
	 * 
	 * @return boolean
	 */
	public function isBoxed()
	{
		$boxTest = $this->selections;
		unset($boxTest['first']);

		return count($boxTest) == 0;
	}

	/**
	 * Total number of selections for all places
	 * 
	 * @return int
	 */
	protected function countSelections()
	{
		$count = 0;
		foreach ($this->selections as $selectionPosition) {
			$count += count($selectionPosition);
		}
		return (int) $count;
	}

    public function getPositionSelectionCount()
    {
        return $this->positionSelectionCount;
    }

    abstract function getCombinationCount();

}
