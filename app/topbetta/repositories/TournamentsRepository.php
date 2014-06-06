<?php
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 6/06/14
 * Time: 1:31 PM
 */

namespace TopBetta\Repositories;

use TopBetta\Tournament;

class TournamentsRepository extends BaseEloquentRepository {

	function __construct(Tournament $tournament) {
		$this->model = $tournament;
	}

	public function getTournamentLeaderboard() {
		return $this->model->leaderboards;
	}

	/**
	 * @return \TopBetta\Tournament
	 */
	public function getTournament()
	{
		return $this->model;
	}

	/**
	 * @param \TopBetta\Tournament $tournament
	 */
	public function setTournament($tournament)
	{
		$this->model = $tournament;
	}
}