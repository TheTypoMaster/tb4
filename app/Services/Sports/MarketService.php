<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/07/2015
 * Time: 2:32 PM
 */

namespace TopBetta\Services\Sports;


use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Services\Markets\MarketOrderingService;
use TopBetta\Services\Resources\Sports\MarketResourceService;

class MarketService {

    /**
     * @var MarketResourceService
     */
    private $marketResourceService;
    /**
     * @var MarketOrderingService
     */
    private $marketOrderingService;
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;

    public function __construct(MarketResourceService $marketResourceService, MarketOrderingService $marketOrderingService, CompetitionRepositoryInterface $competitionRepository)
    {
        $this->marketResourceService = $marketResourceService;
        $this->marketOrderingService = $marketOrderingService;
        $this->competitionRepository = $competitionRepository;
    }

    public function getFilteredMarketsForCompetition($competition, $types = null)
    {
        if( ! $types ) {
            $types = $this->marketOrderingService->getMarketTypeIds($competition->base_competition_id);
        }

        return $this->marketResourceService->getMarketsForCompetition($competition->id, $types);
    }
}