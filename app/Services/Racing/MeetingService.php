<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 10:35 AM
 */

namespace TopBetta\Services\Racing;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;

class MeetingService extends RacingResourceService {

    const RELATION_RACES = 'events';

    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;

    public function __construct(CompetitionRepositoryInterface $competitionRepository)
    {
        $this->competitionRepository = $competitionRepository;
    }

    public function getMeetingsForDate($date, $type = null, $withRaces = false, $withRunners = false)
    {
        $relations = array();

        if( $withRaces ) {
            $relations[] = self::RELATION_RACES;
            $relations = array_merge($relations, array_map(function($v) { return MeetingService::RELATION_RACES . '.' . $v; }, RaceService::getIncludes()));
        }

        if( $withRunners ) {
            $relations[] = self::RELATION_RACES . '.' . RaceService::RELATION_SELECTIONS;
            $relations = array_merge($relations, array_map(function($v) { return MeetingService::RELATION_RACES . '.' . RaceService::RELATION_SELECTIONS . '.' . $v; }, RaceService::getIncludes()));
        }

        return $this->competitionRepository->getRacingCompetitionsByDate(
            Carbon::createFromFormat('Y-m-d H:i:s', $date),
            $type,
            $relations
        );
    }
}