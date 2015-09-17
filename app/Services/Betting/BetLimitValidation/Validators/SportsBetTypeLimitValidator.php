<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/08/2015
 * Time: 2:23 PM
 */

namespace TopBetta\Services\Betting\BetLimitValidation\Validators;


use TopBetta\Services\Betting\BetLimitValidation\Exceptions\BetAmountLimitExceededException;
use TopBetta\Services\Betting\BetLimitValidation\Exceptions\BetLimitExceededException;

class SportsBetTypeLimitValidator extends AbstractBetLimitValidator implements BetLimitValidator {

    protected $limitType = 'bet_sports';

    /**
     * @inheritdoc
     */
    public function validateBet($betData)
    {
        $limit = $this->getBetLimitAmount($betData['user'], $betData['bet_type']);

        //get unique events for selections
        $events = array_unique(array_pluck($betData['selections'], 'event'));

        //get bets for events
        $bets = $this->betRepository->getBetsForUserByEvents($betData['user'], $events);

        //check limit for each event
        foreach (array_unique($events) as $event) {
            //get event selections
            $eventSelections = array_filter($betData['selections'], function($v) use ($event) { return $v['event'] == $event; });

            //get bet total bets amount
            $amount = count($eventSelections) * $betData['amount'] + $bets->filter( function($v) use ($event) {
                    return $v->event_id == $event;
                })->sum('bet_amount');

            //check limit
            if ($amount > $limit) {
                throw new BetAmountLimitExceededException($limit);
            }
        }


    }

}