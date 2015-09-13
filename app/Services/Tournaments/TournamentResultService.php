<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 1/09/2015
 * Time: 3:53 PM
 */

namespace TopBetta\Services\Tournaments;

use TopBetta\Services\Tournaments\Resulting\TournamentProjectedResultService;
use TopBetta\Services\Tournaments\Resulting\TournamentResultService as FinalResultService;

class TournamentResultService {

    /**
     * @var FinalResultService
     */
    private $resultService;
    /**
     * @var TournamentProjectedResultService
     */
    private $projectedResultService;

    public function __construct(FinalResultService $resultService, TournamentProjectedResultService $projectedResultService)
    {
        $this->resultService = $resultService;
        $this->projectedResultService = $projectedResultService;
    }

    public function getTournamentResults($tournament)
    {
        if ($tournament->paid_flag) {
            return $this->resultService->getTournamentResults($tournament);
        }

        return $this->projectedResultService->getTournamentResults($tournament);
    }
}