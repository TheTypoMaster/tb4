<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/07/2015
 * Time: 9:36 AM
 */

namespace TopBetta\Services\Resources\Sports;


use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\Sports\BaseCompetitionResource;
use TopBetta\Resources\Sports\CompetitionResource;
use TopBetta\Resources\Sports\SportResource;

class SportResourceService {

    /**
     * @var SportRepositoryInterface
     */
    private $sportRepository;
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;

    public function __construct(SportRepositoryInterface $sportRepository, CompetitionRepositoryInterface $competitionRepository)
    {
        $this->sportRepository = $sportRepository;
        $this->competitionRepository = $competitionRepository;
    }

    public function getVisibleSportsWithCompetitions($date = null)
    {
        $competitions = $this->competitionRepository->getVisibleCompetitions($date);

        $sports = new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\SportResource');

        foreach($competitions as $competition) {
            if( ! $sport = $sports->get($competition->baseCompetition->sport->id) ) {
                $sports->put($competition->baseCompetition->sport->id, $sport = new SportResource($competition->baseCompetition->sport));
                $sport->setRelation('baseCompetitions', new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\Sports\BaseCompetitionResource'));
            }

            if( ! $baseCompetition = $sport->baseCompetitions->get($competition->baseCompetition->id) ) {
                $sport->baseCompetitions->put($competition->baseCompetition->id, $baseCompetition = new BaseCompetitionResource($competition->baseCompetition));
                $baseCompetition->setRelation('competitions', new EloquentResourceCollection(new Collection(), 'TopBetta\Resource\Sports\CompetitionResource'));
            }


            $baseCompetition->competitions->put($competition->id, new CompetitionResource($competition));
        }


        return $sports;
    }


}