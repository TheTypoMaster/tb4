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
     * @var EventService
     */
    private $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * @inheritdoc
     */
    public function getResultedDividendForBet($bet)
    {
        $totalDividend = 0;
        $dividends = $this->eventService->getExoticDividendsForEventByType($bet->selection->first()->market->event, $bet->type->name);

        if ( ! $dividends ) {
            return 0;
        }

        //check each dividend
        foreach($dividends as $positionString => $dividend) {
            $result = explode('/', $positionString);

            //boxed so just check selections contained in $result
            if( $bet->boxed_flag && count(array_intersect($bet->selection->lists('number')->all(), $result)) == count($bet->selection->lists('number')->all())) {
                $totalDividend += $dividend;
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
                    $totalDividend += $dividend;
                }

            }
        }

        return $totalDividend;
    }

}