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

    public function __construct(SportRepository $sportRepository, CompetitionRepository $competitionRepository)
    {
        $this->sportRepository = $sportRepository;
        $this->competitionRepository = $competitionRepository;
    }

    public function updateCache()
    {
        $this->sportRepository->updateVisibleSportsAndBaseCompetitions();
        $this->competitionRepository->updateVisibleCompetitionsForBaseCompetition();
    }

}