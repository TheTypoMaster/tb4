<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/09/2015
 * Time: 9:41 AM
 */

namespace TopBetta\Services\Resources\Cache\Sports;


use TopBetta\Repositories\Cache\Sports\CompetitionRepository;
use TopBetta\Services\Resources\Cache\CachedResourceService;
use TopBetta\Services\Resources\Sports\CompetitionResourceService;

class CachedCompetitionResourceService extends CachedResourceService {

    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;
    /**
     * @var CachedEventResourceService
     */
    private $eventResourceService;

    public function __construct(CompetitionResourceService $resourceService, CompetitionRepository $competitionRepository, CachedEventResourceService $eventResourceService)
    {
        $this->resourceService = $resourceService;
        $this->competitionRepository = $competitionRepository;
        $this->eventResourceService = $eventResourceService;
    }

    public function getVisibleCompetitionsByBaseCompetition($baseCompetition, $competitionEvents = null)
    {
        $competitions = $this->competitionRepository->getVisibleCompetitionByBaseCompetition($baseCompetition);

        return $competitions;
    }

    public function getCompetitionResource($id)
    {
        $competition = $this->competitionRepository->getCompetition($id);

        if (!$competition) {
            return $this->resourceService->getCompetitionResource($id);
        }

        return $competition;
    }

}