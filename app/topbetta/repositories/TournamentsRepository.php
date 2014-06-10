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

	public function find($id) {
		$model = parent::find($id);
		return $model->toArray();
	}

	public function getTournamentLeaderboard() {
		return $this->model->leaderboards;
	}

	public function getQualifiedLeaderboard($tournamentId) {
		return Tournament::join(
			'tbdb_tournament_leaderboard', 'tbdb_tournament_leaderboard.tournament_id', '=', 'tbdb_tournament.id'
		)->where(
			'tbdb_tournament_leaderboard.tournament_id', '=', $tournamentId
			)
		->where('tbdb_tournament_leaderboard.turned_over', '>=', 'tbdb_tournament.start_currency')
		->orderBy('tbdb_tournament_leaderboard.currency', 'DESC')
		->get()->toArray();
	}

	public function getUsersPosition($userId, $tournamentId) {
		$leaderboard = $this->getQualifiedLeaderboard($tournamentId);
		$previousValue = false;
		$previousRank = null;

		$rank = null;

		for ($i = 0; $i < count($leaderboard); $i++) {

			// Get the record
			$row = $leaderboard[$i];

			// Get the current rows value
			$value = array_get($row, 'currency');

			// Check to see if the current value is the same as the previous value. If they are the same, it means that
			// the rank of the current row is the same as the previous one, so dont bother changing anything, otherwise
			// we need to update the previous rank and value
			if ($previousValue !== $value) {
				$previousRank = $i + 1;
				$previousValue = $value;
			}

			if (array_get($row, 'user_id', false) === $userId) {
				$rank = $previousRank;
				break;
			}
		}

		return $rank;


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