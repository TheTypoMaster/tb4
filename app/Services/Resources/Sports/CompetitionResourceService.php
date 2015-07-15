<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/07/2015
 * Time: 4:32 PM
 */

namespace TopBetta\Services\Resources\Sports;


use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class CompetitionResourceService {

    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;

    public function __construct(CompetitionRepositoryInterface $competitionRepository)
    {
        $this->competitionRepository = $competitionRepository;
    }

    public function getVisibleCompetitions($date = null)
    {
        $competitions = $this->competitionRepository->getVisibleCompetitions($date);

        return new EloquentResourceCollection($competitions, 'TopBetta\Resources\Sports\CompetitionResource');
    }

    public function getVisibleCompetitionsByBaseCompetition($baseCompetition)
    {
        $competitions = $this->competitionRepository->getVisibleCompetitionByBaseCompetition($baseCompetition);

        return new EloquentResourceCollection($competitions, 'TopBetta\Resources\Sports\CompetitionResource');
    }
}