<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/08/2015
 * Time: 1:27 PM
 */

namespace TopBetta\Services\Tournaments;


use TopBetta\Repositories\Contracts\TournamentPlacesPaidRepositoryInterface;

class TournamentPlacesPaidService {

    /**
     * @var TournamentPlacesPaidRepositoryInterface
     */
    private $placesPaidRepository;

    public function __construct(TournamentPlacesPaidRepositoryInterface $placesPaidRepository)
    {
        $this->placesPaidRepository = $placesPaidRepository;
    }

    public function getPercentagesForTournamentByEntrants($tournament)
    {
        $percentages = $this->placesPaidRepository->getByEntrants($tournament->tickets->count());

        if ($tournament->qualifiers->count() < $percentages->places_paid) {
            return $this->getPercentagesByPlacesPaid($tournament->qualifiers->count());
        }

        return $percentages;
    }

    public function getPercentagesByPlacesPaid($placesPaid)
    {
        $percentages = $this->placesPaidRepository->getByPlacesPaid($placesPaid);

        if (!$percentages) {
            throw new \Exception;
        }

        return $percentages;
    }

    public function getPercentagesForTournamentByPlacesPaid($tournament, $placesPaid)
    {
        return $this->getPercentagesByPlacesPaid($tournament->qualifiers->count() < $placesPaid ? $tournament->qualifiers->count() : $placesPaid);
    }
}