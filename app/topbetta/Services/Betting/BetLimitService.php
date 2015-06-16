<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/06/2015
 * Time: 2:55 PM
 */

namespace TopBetta\Services\Betting;

use TopBetta\Repositories\Contracts\BetLimitRepositoryInterface;
use TopBetta\Repositories\Contracts\BetLimitTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Services\Betting\BetSelection\ExoticRacingBetSelectionService;

class BetLimitService {

    /**
     * @var BetLimitRepositoryInterface
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
    /**
     * @var BetLimitTypeRepositoryInterface
     */
    private $betLimitTypeRepository;

    public function __construct(BetLimitRepositoryInterface $betLimitRepository, BetLimitTypeRepositoryInterface $betLimitTypeRepository, BetRepositoryInterface $betRepository, ExoticRacingBetSelectionService $betSelectionService)
    {
        $this->betLimitRepository = $betLimitRepository;
        $this->betRepository = $betRepository;
        $this->betSelectionService = $betSelectionService;
        $this->betLimitTypeRepository = $betLimitTypeRepository;
    }

    /**
     * Bet Limit for racing win place bets
     * @param $user
     * @param $betAmount
     * @param $selection
     * @param $betType
     * @return null
     */
    public function getWinPlaceBetLimitExceeded($user, $betAmount, $selection, $betType)
    {
        $limit = $this->betLimitRepository->getLimitForUserAndBetType($user->id, $betType);

        if ( ! $limit ) {
            $limit = $this->getDefaultRacingBetLimit();
        }

        $currentBetAmount = $this->betRepository->getBetsForUserBySelection($user->id, $selection->id, $betType)->sum(function($v) { return $v->bet_amount; });

        if( $currentBetAmount + $betAmount >  $limit ) {
            return $limit;
        }

        return null;
    }

    /**
     * Bet limit for exotic racing bets
     * @param $user
     * @param $betAmount
     * @param $percentage
     * @param $selections
     * @param $betType
     * @return array
     */
    public function getExoticBetLimitsExceeded($user, $betAmount, $percentage, $selections, $betType)
    {
        $limit = $this->betLimitRepository->getLimitForUserAndBetType($user->id, $betType);
        $flexiLimit = $this->betLimitRepository->getLimitForUserAndBetType($user->id, $betType, BetLimitTypeRepositoryInterface::BET_LIMIT_FLEXI) / 100;

        if ( ! $limit ) {
            $limit = $this->getDefaultRacingBetLimit();
        }

        if( ! $flexiLimit ) {
            $flexiLimit = $this->getDefaultFlexiBetLimit();
        }

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

    /**
     * Bet limit for sports
     * @param $user
     * @param $betAmount
     * @param $selection
     * @param $betType
     * @return null
     */
    public function getSportsBetLimitExceeded($user, $betAmount, $selection, $betType)
    {
        $limit = $this->betLimitRepository->getLimitForUserAndBetType($user->id, $betType, BetLimitTypeRepositoryInterface::BET_LIMIT_SPORT);

        $currentBetAmount = $this->betRepository->getBetsForUserBySelection($user->id, $selection->id)->sum(function($v) { return $v->bet_amount; });

        if( $currentBetAmount + $betAmount > $limit ) {
            return $limit;
        }

        return null;
    }

    public function getDefaultRacingBetLimit()
    {
        return $this->betLimitTypeRepository->getByName(BetLimitTypeRepositoryInterface::BET_LIMIT_DEFAULT)->first()->default_amount;
    }

    public function getDefaultFlexiBetLimit()
    {
        return $this->betLimitTypeRepository->getByName(BetLimitTypeRepositoryInterface::BET_LIMIT_FLEXI_DEFAULT)->first()->default_amount;
    }

    public function getDefaultSportsBetLimit()
    {
        return $this->betLimitTypeRepository->getByName(BetLimitTypeRepositoryInterface::BET_LIMIT_SPORT_DEFAULT)->first()->default_amount;
    }

}