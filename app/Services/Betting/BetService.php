<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 17/07/2015
 * Time: 12:56 PM
 */

namespace TopBetta\Services\Betting;


use Carbon\Carbon;
use TopBetta\Services\Resources\Betting\BetResourceService;

class BetService {

    /**
     * @var BetResourceService
     */
    private $betResourceService;

    public function __construct(BetResourceService $betResourceService)
    {
        $this->betResourceService = $betResourceService;
    }


    public function getBetHistory($user, $criteria = 'all', $order = null)
    {
        if( $order ) {
            $this->betResourceService->setOrder(
                array_get($order, 0), array_get($order, 1)
            );
        }

        switch($criteria)
        {
            case 'all':
                return $this->betResourceService->getAllBetsForUser($user);
            case 'unresulted':
                return $this->betResourceService->getUnresultedBetsForUser($user);
            case 'winning':
                return $this->betResourceService->getWinningBetsForUser($user);
            case 'losing' :
                return $this->betResourceService->getLosingBetsForUser($user);
            case 'refunded':
                return $this->betResourceService->getRefundedBetsForUser($user);
        }

        return $this->betResourceService->getAllBetsForUser($user);
    }

    public function getActiveAndRecentBetsForUser($user)
    {
        $date = Carbon::now()->subHours(6);

        $active = $this->betResourceService->getUnresultedBetsForUser($user, false);
        $recent = $this->betResourceService->getBetsOnDateForUser($user, $date, true);

        return $active->merge($recent);
    }

    public function getBetsForDate($user, $date)
    {
        $date = Carbon::createFromFormat('Y-m-d', $date);

        return $this->betResourceService->getBetsOnDateForUser($user, $date);
    }

    public function getBetsForEventGroup($user, $eventGroup)
    {
        return $this->betResourceService->getBetsForEventGroup($user, $eventGroup);
    }
}