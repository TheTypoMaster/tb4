<?php
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 6/06/14
 * Time: 1:31 PM
 */

namespace TopBetta\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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
//		return $this->model->leaderboards;
		Tournament::join(
			'tbdb_tournament_leaderboard', 'tbdb_tournament_leaderboard.tournament_id', '=', 'tbdb_tournament.id'
		)->get()->toArray();
	}

	public function getQualifiedLeaderboard($tournamentId) {
		return Tournament::join(
			'tbdb_tournament_leaderboard', 'tbdb_tournament_leaderboard.tournament_id', '=', 'tbdb_tournament.id'
		)->where(
			'tbdb_tournament_leaderboard.tournament_id', '=', $tournamentId
			)
		->whereRaw('tbdb_tournament_leaderboard.turned_over >= tbdb_tournament.start_currency')
		->orderBy('tbdb_tournament_leaderboard.currency', 'DESC')
		->get()->toArray();
	}

	public function getCachedPaidTournamentLeaderboards($userId, $tournamentId, $minutes = 60) {
		$tournamentsRepository = $this;
		return Cache::remember("tournament-$tournamentId-leaderboard", $minutes, function() use ($tournamentsRepository, $tournamentId, $userId, $tournamentId) {
			return $tournamentsRepository->getUsersPosition($userId, $tournamentId);
		});
	}

	public function getNonCachedTournamentLeaderboards($userId, $tournamentId, $minutes = 60) {
		$now = new Carbon();
		return $this->getUsersPosition($userId, $tournamentId);
	}

	public function getUsersPosition($userId, $tournamentId) {

		$tournament = $this->find($tournamentId);
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
				$rank = $row;
				$rank['position'] = $previousRank;
				break;
			}
		}

		$rank['total_entrants'] = count($this->getTournamentLeaderboard());

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