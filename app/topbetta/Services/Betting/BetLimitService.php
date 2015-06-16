<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/06/2015
 * Time: 2:55 PM
 */

namespace TopBetta\Services\Betting;

use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\DbBetLimitRepository;
use TopBetta\Services\Betting\BetSelection\ExoticRacingBetSelectionService;

class BetLimitService {

    /**
     * @var DbBetLimitRepository
     */
    private $betLimitRepository;
    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;
    /**
     * @var ExoticRacingBetSelectionService
     */
    private $betSelectionService;

    public function __construct(DbBetLimitRepository $betLimitRepository, BetRepositoryInterface $betRepository, ExoticRacingBetSelectionService $betSelectionService)
    {
        $this->betLimitRepository = $betLimitRepository;
        $this->betRepository = $betRepository;
        $this->betSelectionService = $betSelectionService;
    }


    public function getWinPlaceBetLimitExceeded($user, $betAmount, $selection, $betType)
    {
        $limit = $this->betLimitRepository->getLimitForUserAndBetType($user->id, $betType);

        $currentBetAmount = $this->betRepository->getBetsForUserBySelection($user->id, $selection->id, $betType)->sum(function($v) { return $v->bet_amount; });

        if( $currentBetAmount + $betAmount >  $limit ) {
            return $limit;
        }

        return false;
    }

    public function getExoticBetLimitsExceeded($user, $betAmount, $percentage, $selections, $betType)
    {
        $limit = $this->betLimitRepository->getLimitForUserAndBetType($user->id, $betType);
        $flexiLimit = $this->betLimitRepository->getLimitForUserAndBetType($user->id, $betType, 'bet_flexi');

        $bets = $this->betRepository->getBetsForUserByEvent($user->id, $selections[0]['selection']->id, $betType);

        $currentAmount = 0;
        $flexiPercentage = 0;

        //check selections match
        foreach($bets as $bet) {
            if( $bet->selection_string === $this->betSelectionService->getSelectionString($selections) ) {
                $currentAmount += $bet->bet_amount;
                $flexiPercentage += $bet->percentage;
            }
        }

        $limits = array();

        if( $currentAmount + $betAmount > $limit ) {
            $limits['amount'] = $limit;
        }

        if( $flexiPercentage + $percentage > $flexiLimit ) {
            $limits['percentage'] = $flexiLimit;
        }

        return $limits;
    }

    public function getSportsBetLimitExceeded($user, $betAmount, $selection, $betType)
    {
        $limit = $this->betLimitRepository->getLimitForUserAndBetType($user->id, $betType, 'bet_sports');

        $currentBetAmount = $this->betRepository->getBetsForUserBySelection($user->id, $selection->id)->sum(function($v) { return $v->bet_amount; });


    }

}