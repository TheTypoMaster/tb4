<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 12:41 PM
 */

namespace TopBetta\Services\Betting\BetDividend\BetTypeDividend;


use TopBetta\Services\Betting\SelectionService;

class WinBetTypeDividendService extends AbstractBetTypeDividendService {

    /**
     * @var SelectionService
     */
    private $selectionService;

    public function __construct(SelectionService $selectionService)
    {
        $this->selectionService = $selectionService;
    }

    public function getResultedDividendForBet($bet)
    {
        if( $bet->selection->first()->result && $bet->selection->first()->result->position == 1 ) {
            if( $bet->betselection->first()->fixed_odds && $this->selectionService->isSelectionSports($bet->selection->first()->id) ) {
                return $bet->betselection->first()->fixed_odds;
            }

            return $bet->selection->first()->result->win_dividend;
        }

        return 0;
    }
}