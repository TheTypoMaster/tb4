<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/07/2015
 * Time: 4:40 PM
 */

namespace TopBetta\Services\Sports;


use Illuminate\Database\Eloquent\Collection;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\Sports\BaseCompetitionResource;
use TopBetta\Resources\Sports\SportResource;
use TopBetta\Services\Resources\Sports\CompetitionResourceService;

class SportsService {

    /**
     * @var CompetitionResourceService
     */
    private $competitionResourceService;

    public function __construct(CompetitionResourceService $competitionResourceService)
    {
        $this->competitionResourceService = $competitionResourceService;
    }

    public function getSportsWithCompetitions($date = null)
    {
        $competitions = $this->competitionResourceService->getVisibleCompetitions($date);

        $sports = new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\SportResource');

        foreach($competitions as $competition) {
            if( ! $sport = $sports->get($competition->baseCompetition->sport->id) ) {
                $sports->put($competition->baseCompetition->sport->id, $sport = new SportResource($competition->baseCompetition->sport));
                $sport->setRelation('baseCompetitions', new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\Sports\BaseCompetitionResource'));
            }

            if( ! $baseCompetition = $sport->baseCompetitions->get($competition->baseCompetition) ) {
                $sport->baseCompetitions->put($competition->baseCompetition->id, $baseCompetition = new BaseCompetitionResource($competition->baseCompeition));
                $baseCompetition->setRelation('competitions', new EloquentResourceCollection(new Collection(), 'TopBetta\Resource\Sports\CompetitionResource'));
            }

            $baseCompetition->competitions->put($competition->id, $competition);
        }


        return $sports;
    }
}