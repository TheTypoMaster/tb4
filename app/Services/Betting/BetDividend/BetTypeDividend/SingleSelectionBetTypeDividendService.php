<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 12:41 PM
 */

namespace TopBetta\Services\Betting\BetDividend\BetTypeDividend;


use TopBetta\Repositories\Contracts\ResultPricesRepositoryInterface;
use TopBetta\Services\Betting\SelectionService;

class SingleSelectionBetTypeDividendService extends AbstractBetTypeDividendService {

    /**
     * @inheritdoc
     */
    public function getResultedDividendForBet($bet)
    {
        if( $bet->selection->first()->result ) {
            if( $this->selectionService->isSelectionSports($bet->selection->first()->id) || $bet->product->is_fixed_odds ) {
                return $this->calculateFixedOddsDividend($bet);
            }

            //get the tote result price
            return ($price = $this->resultPricesRepository->getPriceForResultByProductAndBetType(
                $bet->selection->first()->result->id,
                $bet->bet_product_id,
                $bet->bet_type_id
            )) ? $price->dividend : 0;
        }

        return 0;
    }

    public function calculateFixedOddsDividend($bet)
    {
        $deductions = $this->selectionService->totalDeduction($bet->selection->first()->market_id, $bet->type->name);

        //hack for tournament fixed odds stored differently
        $odds = $bet->betselection->first()->fixed_odds ? : $bet->fixed_odds;

        $odds =  $odds - ($deductions/100) * $odds;

        //check for dead heats
        $runnersInPosition = $this->resultRepository->getResultsForEventByPosition($bet->event_id, $bet->selection->first()->result->position)->count();

        return $odds/$runnersInPosition;
    }
}