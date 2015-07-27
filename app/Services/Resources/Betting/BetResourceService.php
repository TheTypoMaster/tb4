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
use TopBetta\Services\Resources\OrderableResourceService;

class BetResourceService extends OrderableResourceService {

    /**
     * @var BetRepositoryInterface
     */
    protected $repository;

    protected $orderFields = array(
        'id' => 'id',
        'amount' => 'bet_amount',
        'free_credit_amount' => 'bet_freebet_amount',
        'selection_id' => 'selection_id',
        'selection_name' => 'selection_name',
        'selection_string' => 'selection_string',
        'market_name' => 'market_name',
        'market_id' => 'market_id',
        'event_name' => 'event_name',
        'event_id' => 'event_id',
        'competition_name' => 'competition_name',
        'competition_id' => 'competition_id',
        'bet_type' => 'bet_type',
        'status' => 'status',
        'paid' => 'won_amount',
        'date' => 'start_date'
    );

    public function __construct(BetRepositoryInterface $betRepository)
    {
        $this->repository = $betRepository;
    }

    public function getAllBetsForUser($user)
    {
        $bets = $this->repository->getAllBetsForUser($user);

        return $this->createBetsCollection($bets);
    }

    public function getUnresultedBetsForUser($user, $page = true)
    {
        $bets = $this->repository->getUnresultedBetsForUser($user, $page);

        return $this->createBetsCollection($bets, $page);
    }

    public function getWinningBetsForUser($user)
    {
        $bets = $this->repository->getWinningBetsForUser($user);

        return $this->createBetsCollection($bets);
    }

    public function getLosingBetsForUser($user)
    {
        $bets = $this->repository->getLosingBetsForUser($user);

        return $this->createBetsCollection($bets);
    }

    public function getRefundedBetsForUser($user)
    {
        $bets = $this->repository->getRefundedBetsForUser($user);

        return $this->createBetsCollection($bets);
    }

    public function getBetsOnDateForUser($user, Carbon $date, $resulted = null)
    {
        $bets = $this->repository->getBetsOnDateForUser($user, $date, $resulted);

        return new EloquentResourceCollection($bets, 'TopBetta\Resources\Betting\BetResource');
    }

    public function getBetsForEventGroup($user, $eventGroup)
    {
        return $this->createBetsCollection(
            $this->repository->getBetsForEventGroup($user, $eventGroup),
            false
        );
    }

    public function getBetsByEventForAuthUser($event)
    {
        if( ! \Auth::user() ) {
            return array();
        }

        return $this->createBetsCollection(
            $this->repository->getBetsForUserByEvent(\Auth::user()->id, $event),
            false
        );
    }

    protected function createBetsCollection($bets, $page = true)
    {
        if( $page ) {
            return new PaginatedEloquentResourceCollection($bets, 'TopBetta\Resources\Betting\BetResource');
        }

        return new EloquentResourceCollection($bets, 'TopBetta\Resources\Betting\BetResource');
    }
}