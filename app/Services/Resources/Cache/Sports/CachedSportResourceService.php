<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/09/2015
 * Time: 5:39 PM
 */

namespace TopBetta\Services\Resources\Cache\Sports;


use TopBetta\Repositories\Cache\Sports\CompetitionRepository;
use TopBetta\Repositories\Cache\Sports\SportRepository;
use TopBetta\Services\Resources\Cache\CachedResourceService;
use TopBetta\Services\Resources\Sports\SportResourceService;

class CachedSportResourceService extends CachedResourceService {

    /**
     * @var SportRepository
     */
    private $sportRepository;
    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;

    public function __construct(SportResourceService $resourceService, SportRepository $sportRepository, CompetitionRepository $competitionRepository)
    {
        $this->resourceService = $resourceService;
        $this->sportRepository = $sportRepository;
        $this->competitionRepository = $competitionRepository;
    }

    public function getVisibleSportsWithCompetitions($date = null)
    {
        if ($date) {
            return $this->resourceService->getVisibleSportsWithCompetitions($date);
        }

        $sports = $this->sportRepository->getVisibleSportsAndBaseCompetitions();

        $sports = $this->attachCompetitions($sports);

        return $sports;
    }


    protected function attachCompetitions($sports)
    {
        foreach($sports as $sport) {
            foreach ($sport->baseCompetitions as $baseCompetition) {
                $baseCompetition->setRelation('competitions', $this->competitionRepository->getCompetitionsForBaseCompetition($baseCompetition->id));
            }
        }

        return $sports;
    }
}