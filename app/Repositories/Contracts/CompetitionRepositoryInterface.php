<?php namespace TopBetta\Repositories\Contracts;
use Carbon\Carbon;

/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 21:17
 * Project: tb4
 */

interface CompetitionRepositoryInterface {

    public function findAllSportsCompetitions($paged = null);

    public function getVisibleCompetitions(Carbon $date = null);
} 