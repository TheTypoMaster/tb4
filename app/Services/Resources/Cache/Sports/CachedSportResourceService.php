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
    /**
     * @var CachedBaseCompetitionResourceService
     */
    private $baseCompetitionResourceService;

    public function __construct(SportResourceService $resourceService, SportRepository $sportRepository, CachedCompetitionResourceService $competitionResourceService, CachedBaseCompetitionResourceService $baseCompetitionResourceService)
    {
        $this->resourceService = $resourceService;
        $this->sportRepository = $sportRepository;
        $this->competitionResourceService = $competitionResourceService;
        $this->baseCompetitionResourceService = $baseCompetitionResourceService;
    }

    public function getVisibleSportsWithCompetitions($date = null)
    {
        if ($date) {
            return $this->resourceService->getVisibleSportsWithCompetitions($date);
        }

        $sports = $this->sportRepository->getVisibleSports();

        $sports = $this->attachCompetitions($sports);

        return $this->filterSports($sports);
    }

    public function getVisibleSportsWithSelectedCompetition($competition)
    {
        $sports = $this->sportRepository->getVisibleSports();

        return $this->filterSports($sports, $competition);
    }

    public function getVisibleSports($sportId = null)
    {
        $sports = $this->sportRepository->getVisibleSports();

        return $this->filterSports($sports, $sportId);
    }

    public function filterSports($sports, $sportId = null)
    {
        return $sports->filter(function ($v) use ($sportId) {
            if ($v->id == $sportId) {
                return true;
            }

            $baseCompetitions = $this->baseCompetitionResourceService->getBaseCompetitionForSport($v->id);
            return (bool) ($baseCompetitions->count() > 0 && $v->display_flag);
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