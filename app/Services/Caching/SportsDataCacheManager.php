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
     * @var EventRepository
     */
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function updateCache($n2jLimit = 10)
    {
        $this->eventRepository->updateNextToJump($n2jLimit);

    }

}