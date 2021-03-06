<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 8/05/2015
 * Time: 4:30 PM
 */

namespace TopBetta\Services\Events;

use TopBetta\Repositories\Contracts\PlayersRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Repositories\Contracts\TeamRepositoryInterface;

class CompetitorService {

    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;
    /**
     * @var TeamRepositoryInterface
     */
    private $teamRepository;
    /**
     * @var PlayersRepositoryInterface
     */
    private $playersRepository;

    public function __construct(SelectionRepositoryInterface $selectionRepository, TeamRepositoryInterface $teamRepository, PlayersRepositoryInterface $playersRepository)
    {
        $this->selectionRepository = $selectionRepository;
        $this->teamRepository = $teamRepository;
        $this->playersRepository = $playersRepository;
    }

    public function getCompetitorByExternalId($externalId)
    {
        //try to get team first
        $competitor = $this->teamRepository->findByExternalId($externalId);

        if( ! $competitor ) {
            $competitor = $this->playersRepository->findByExternalId($externalId);
        }

        return $competitor;
    }

    public function addCompetitorToSelection($selectionId, $competitorId, $extCompetitorId, $competitorType)
    {
        $competitor = null;

        if ($competitorType == 'team') {
            $competitor = $this->teamRepository->getBySerenaId($competitorId);
        } else if ($competitorType == 'player') {
            $competitor = $this->playersRepository->getBySerenaId($competitorId);
        }

        if (! $competitor) {
            $competitor = $this->getCompetitorByExternalId($competitorId);
        }

        if( $competitor && ! in_array($selectionId, $competitor->selections->lists('id')->all()) ) {
            $competitor->selections()->attach(array($selectionId));
        }

        return $competitor;

    }

    public function addCompetitorModelToSelection($selection, $competitor)
    {
        if (!in_array((int)$selection->id, $competitor->selections->lists('id')->all())) {
            $competitor->selections()->attach($selection->id);
        }

        return $competitor;
    }
}