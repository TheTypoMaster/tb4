<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/09/2015
 * Time: 3:16 PM
 */

namespace TopBetta\Services\Resources\Cache\Sports;


use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\Sports\BaseCompetitionRepository;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\Sports\BaseCompetitionResource;
use TopBetta\Services\Resources\Cache\CachedResourceService;

class CachedBaseCompetitionResourceService {


    /**
     * @var BaseCompetitionRepository
     */
    private $baseCompetitionRepository;
    /**
     * @var CachedCompetitionResourceService
     */
    private $competitionResourceService;

    public function __construct(BaseCompetitionRepository $baseCompetitionRepository, CachedCompetitionResourceService $competitionResourceService)
    {
        $this->baseCompetitionRepository = $baseCompetitionRepository;
        $this->competitionResourceService = $competitionResourceService;
    }

    public function getBaseCompetitionForSport($sport)
    {
        $baseCompetitions = $this->baseCompetitionRepository->getBaseCompetitionsBySport($sport);

        if (!$baseCompetitions) {
            return new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\Sports\BaseCompetitionResource');
        }

        return $baseCompetitions;
    }

    public function getBaseCompetitionsForSportWithCompetitions($sport, $competition = null)
    {
        $baseCompetitions = $this->baseCompetitionRepository->getBaseCompetitionsBySport($sport);

        if (!$baseCompetitions) {
            return new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\Sports\BaseCompetitionResource');
        }

        $baseCompetitions->each(function($v) use ($competition) {
            $v->setRelation('competitions', $this->competitionResourceService->getVisibleCompetitionsByBaseCompetition($v->id, $competition));
        });

        return $baseCompetitions;
    }

    public function getBaseCompetitionForCompetitionId($competition)
    {
        $competition = $this->competitionResourceService->getCompetitionResource($competition);

        $baseComp = $this->baseCompetitionRepository->getBaseCompetition($competition->base_competition_id);

        if (!$baseComp) {
            return new BaseCompetitionResource($this->baseCompetitionRepository->find($competition->base_competition_id));
        }

        return $baseComp;
    }


}