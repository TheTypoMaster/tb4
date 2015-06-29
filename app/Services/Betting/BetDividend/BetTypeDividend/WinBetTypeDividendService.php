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

    /**
     * @inheritdoc
     */
    public function getResultedDividendForBet($bet)
    {
        if( $bet->selection->first()->result && ($bet->selection->first()->result->position == 1 ||$bet->selection->first()->result->position == null) ) {
            if( ($bet->betselection->first()->fixed_odds || $bet->fixed_odds) && $this->selectionService->isSelectionSports($bet->selection->first()->id) ) {
                //hack for tournament fixed odds stored differently
                return $bet->betselection->first()->fixed_odds ? : $bet->fixed_odds;
            }

            return $bet->selection->first()->result->win_dividend;
        }

        return 0;
    }
}