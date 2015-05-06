<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/02/2015
 * Time: 3:47 PM
 */

namespace TopBetta\Services\Betting;


use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionStatusRepositoryInterface;

class SelectionService {

    //do in DB
    const SELECTION_NOT_SCRATCHED = "not scratched";

    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;
    /**
     * @var SelectionStatusRepositoryInterface
     */
    private $selectionStatusRepository;
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;

    public function __construct(SelectionRepositoryInterface $selectionRepository, SelectionStatusRepositoryInterface $selectionStatusRepository, CompetitionRepositoryInterface $competitionRepository)
    {
        $this->selectionRepository = $selectionRepository;
        $this->selectionStatusRepository = $selectionStatusRepository;
        $this->competitionRepository = $competitionRepository;
    }

    public function isSelectionAvailableForBetting($selectionId)
    {
        $selection = $this->selectionRepository->find($selectionId);

        if( $selection->selection_status_id == $this->selectionStatusRepository->getSelectionStatusIdByName(self::SELECTION_NOT_SCRATCHED) ) {
            return true;
        }

        return false;
    }

    public function isSelectionRacing($selectionId)
    {
        $event = $this->competitionRepository->getCompetitionBySelection($selectionId);

        return $event->sport_id == 0;
    }

    public function isSelectionSports($selectionId)
    {
        $event = $this->competitionRepository->getCompetitionBySelection($selectionId);

        return $event->sport_id > 0;
    }

}