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

        return $this->filter($baseCompetitions);
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

        return $this->filterWithCompetitions($baseCompetitions, $competition);
    }

    public function getBaseCompetitionForCompetitionId($competition)
    {
        $competition = $this->competitionResourceService->getCompetitionResource($competition);

        return $this->baseCompetitionRepository->getBaseCompetition($competition->base_competition_id);
    }

    public function filter($baseCompetitions)
    {
        return $baseCompetitions->filter(function($v) {
            $competitions = $this->competitionResourceService->getVisibleCompetitionsByBaseCompetition($v->id);

            return (bool) ($competitions->count() > 0 && $v->display_flag);
        });
    }

    public function filterWithCompetitions($baseCompetitions)
    {
        return $baseCompetitions->filter(function($v) {
            $competitions = $v->competitions;

            return (bool) ($competitions->count() > 0 && $v->display_flag);
        });
    }

}