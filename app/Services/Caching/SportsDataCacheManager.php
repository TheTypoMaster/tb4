<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/09/2015
 * Time: 4:53 PM
 */

namespace TopBetta\Services\Caching;


use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Cache\Sports\CompetitionRepository;
use TopBetta\Repositories\Cache\Sports\EventRepository;
use TopBetta\Repositories\Cache\Sports\MarketRepository;
use TopBetta\Repositories\Cache\Sports\MarketTypeRepository;
use TopBetta\Repositories\Cache\Sports\SportRepository;

class SportsDataCacheManager {

    /**
     * @var SportRepository
     */
    private $sportRepository;
    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;
    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var MarketRepository
     */
    private $marketRepository;
    /**
     * @var MarketTypeRepository
     */
    private $marketTypeRepository;

    public function __construct(SportRepository $sportRepository,
                                CompetitionRepository $competitionRepository,
                                EventRepository $eventRepository,
                                MarketRepository $marketRepository,
                                MarketTypeRepository $marketTypeRepository)
    {
        $this->sportRepository = $sportRepository;
        $this->competitionRepository = $competitionRepository;
        $this->eventRepository = $eventRepository;
        $this->marketRepository = $marketRepository;
        $this->marketTypeRepository = $marketTypeRepository;
    }

    public function updateCache()
    {
        $this->sportRepository->updateVisibleSportsAndBaseCompetitions();
        $this->competitionRepository->updateVisibleCompetitionsForBaseCompetition();
        $this->eventRepository->updateEvents();
        $this->eventRepository->updateNextToJump();
        $this->marketRepository->updateMarketsAndSelections();
        $this->marketTypeRepository->updateMarketTypes();
    }

}