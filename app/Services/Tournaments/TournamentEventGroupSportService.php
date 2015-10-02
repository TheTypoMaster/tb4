<?php

namespace TopBetta\Services\Tournaments;


use TopBetta\Repositories\TournamentEventGroupSportRepository;

class TournamentEventGroupSportService
{

    public function __construct(TournamentEventGroupSportRepository $tournamentEventGroupSportRepository)
    {
        $this->tournamentEventGroupSportRepository = $tournamentEventGroupSportRepository;
    }

    /**
     * create tournament event group sport relationship
     * @param $data
     * @return static
     */
    public function createTourEventGroupSport($data) {
        return $this->tournamentEventGroupSportRepository->createTourEventGroupSport($data);
    }

    /**
     * get tournament event group sport relation
     * @param $data
     * @return mixed
     */
    public function getTourEventGroupSport($data) {
        return $this->tournamentEventGroupSportRepository->getTourEventGroupSport($data);
    }

}