<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 17/07/2015
 * Time: 12:03 PM
 */

namespace TopBetta\Services\Resources\Betting;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\PaginatedEloquentResourceCollection;

class BetResourceService {

    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;

    public function __construct(BetRepositoryInterface $betRepository)
    {
        $this->betRepository = $betRepository;
    }

    public function getAllBetsForUser($user, $page = null)
    {
        $bets = $this->betRepository->getAllBetsForUser($user, $page);

        return $this->createBetsCollection($bets, $page);
    }

    public function getUnresultedBetsForUser($user, $page = null)
    {
        $bets = $this->betRepository->getUnresultedBetsForUser($user, $page);

        return $this->createBetsCollection($bets, $page);
    }

    public function getWinningBetsForUser($user, $page = null)
    {
        $bets = $this->betRepository->getWinningBetsForUser($user, $page);

        return $this->createBetsCollection($bets, $page);
    }

    public function getLosingBetsForUser($user, $page = null)
    {
        $bets = $this->betRepository->getLosingBetsForUser($user, $page);

        return $this->createBetsCollection($bets, $page);
    }

    public function getRefundedBetsForUser($user, $page = null)
    {
        $bets = $this->betRepository->getRefundedBetsForUser($user, $page);

        return $this->createBetsCollection($bets, $page);
    }

    public function getBetsOnDateForUser($user, Carbon $date, $resulted = null)
    {
         $bets = $this->betRepository->getBetsOnDateForUser($user, $date, $resulted);

        return $this->createBetsCollection($bets);
    }

    public function getBetsForEventGroup($user, $eventGroup)
    {
        return $this->createBetsCollection(
            $this->betRepository->getBetsForEventGroup($user, $eventGroup)
        );
    }

    protected function createBetsCollection($bets, $page = null)
    {
        if( ! is_null($page) ) {
            return new PaginatedEloquentResourceCollection($bets, 'TopBetta\Resources\Betting\BetResource');
        }

        return new EloquentResourceCollection($page ? $bets->getCollection() : $bets, 'TopBetta\Resources\Betting\BetResource');
    }
}