<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 17/07/2015
 * Time: 12:56 PM
 */

namespace TopBetta\Services\Betting;


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


    public function getBetHistory($user, $criteria = 'all', $page = null)
    {
        switch($criteria)
        {
            case 'all':
                return $this->betResourceService->getAllBetsForUser($user, $page);
            case 'unresulted':
                return $this->betResourceService->getUnresultedBetsForUser($user, $page);
            case 'winning':
                return $this->betResourceService->getWinningBetsForUser($user, $page);
            case 'losing' :
                return $this->betResourceService->getLosingBetsForUser($user, $page);
            case 'refunded':
                return $this->betResourceService->getRefundedBetsForUser($user, $page);
        }

        return $this->betResourceService->getAllBetsForUser($user, $page);
    }
}