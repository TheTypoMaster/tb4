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
     * @var CachedCompetitionResourceService
     */
    private $competitionResourceService;

    public function __construct(SportResourceService $resourceService, SportRepository $sportRepository, CachedCompetitionResourceService $competitionResourceService)
    {
        $this->resourceService = $resourceService;
        $this->sportRepository = $sportRepository;
        $this->competitionResourceService = $competitionResourceService;
    }

    public function getVisibleSportsWithCompetitions($date = null)
    {
        if ($date) {
            return $this->resourceService->getVisibleSportsWithCompetitions($date);
        }

        $sports = $this->sportRepository->getVisibleSportsAndBaseCompetitions();

        $sports = $this->attachCompetitions($sports);

        return $this->filterSports($sports);
    }

    public function filterSports($sports)
    {

        $sports = $sports->map(function ($v) {
            $v->setRelation('baseCompetitions', $v->baseCompetitions->filter(function ($q) {

                return (bool) ($q->competitions->count() && $q->display_flag);
            }));

            return $v;
        });

        return $sports->filter(function ($v) {
            return (bool) ($v->baseCompetitions->count() && $v->display_flag);
        });
    }


    protected function attachCompetitions($sports)
    {
        foreach($sports as $sport) {
            foreach ($sport->baseCompetitions as $baseCompetition) {
                $baseCompetition->setRelation('competitions', $this->competitionResourceService->getVisibleCompetitionsByBaseCompetition($baseCompetition->id));
            }
        }

        return $sports;
    }
}