<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 12:45 PM
 */

namespace TopBetta\Services\Betting\BetDividend\BetTypeDividend;

use TopBetta\Services\Betting\EventService;

class ExoticBetTypeDividendService extends AbstractBetTypeDividendService {

    /**
     * @inheritdoc
     */
    public function getResultedDividendForBet($bet)
    {
        $totalDividend = 0;
        $dividends = $this->resultPricesRepository->getPricesByProductAndBetType($bet->bet_product_id, $bet->bet_type_id);

        if ( ! $dividends ) {
            return 0;
        }

        //check each dividend
        foreach($dividends as $dividend) {
            $result = explode('/', $dividend->result_string);

            //boxed so just check selections contained in $result
            if( $bet->boxed_flag && count(array_intersect($result, $bet->selection->lists('number')->all())) == count($result)) {
                $totalDividend += $dividend->dividend;
            } else {
                $hasSelections = true;
                //check we have a selection in each position
                foreach($result as $position => $number) {
                    //get selection in position
                    $betSelection = $bet->betselection->filter(function ($v) use ($position, $number) {
                        return $v->position == $position+1 && $v->selection->number == $number;
                    });

                    //does selection exists
                    if ( ! $betSelection->count() ) {
                        $hasSelections = false;
                    }

                }

                //exotic is winning so add dividend
                if( $hasSelections ) {
                    $totalDividend += $dividend->dividend;
                }

            }
        }

        return $totalDividend;
    }

}