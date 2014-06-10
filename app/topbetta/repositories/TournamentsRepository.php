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

	public function getUsersPosition($userId) {

		$leaderboard = $this->getTournamentLeaderboard();

		$tournaments = $this->getTournament()->join(
			'tbdb_tournament_leaderboard', 'tbdb_tournament_leaderboard.tournament_id', '=', 'tbdb_tournament.id'
		)->get();

		dd($tournaments);

		foreach ($leaderboard as $leaderboardRow) {



		}


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