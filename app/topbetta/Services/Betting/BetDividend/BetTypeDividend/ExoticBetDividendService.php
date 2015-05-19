<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 12:45 PM
 */

namespace TopBetta\Services\Betting\BetType\BetTypeDividend;


use TopBetta\Services\Betting\EventService;

abstract class ExoticBetDividendService {

    /**
     * @var EventService
     */
    private $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function getResultedBetDividend($bet)
    {
        $totalDividend = 0;
        $dividends = $this->eventService->getExoticDividendsForEventByType($bet->event_id, $bet->type->name);

        //check each dividend
        foreach($dividends as $positionString => $dividend) {
            $result = explode('/', $positionString);

            //boxed so just check selections contained in $result
            if( $bet->boxed_flag && count(array_intersect($bet->selection->lists('number'), $result)) == count($bet->selections->lists('number'))) {
                $totalDividend += $dividend;
            } else {
                $hasSelections = true;
                //check we have a selection in each position
                foreach($result as $position => $number) {
                    //get selection in position
                    $betSelection = $bet->betselection->filter(function ($v) use ($position, $number) {
                        return $v->position == $position+1 && $v->selection->number == number;
                    });

                    //does selection exists
                    if ( ! $betSelection->count() ) {
                        $hasSelections = false;
                    }

                }

                if( $hasSelections ) {
                    $totalDividend += $dividend;
                }

            }
        }

        return $totalDividend;
    }

}