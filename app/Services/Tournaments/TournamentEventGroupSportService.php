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

}