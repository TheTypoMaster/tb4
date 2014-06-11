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
use TopBetta\TournamentLeaderboard;

class TournamentsRepository extends BaseEloquentRepository {

	function __construct(Tournament $tournament) {
		$this->model = $tournament;
	}

	public function find($id) {
		$model = parent::find($id);
		return $model->toArray();
	}

	public function getLeaderboardCount($tournamentId) {
		return TournamentLeaderboard::where('tournament_id', '=', $tournamentId)->count();
	}

	public function getQualifiedLeaderboard($tournamentId) {
		return Tournament::select(\DB::raw('*, tbdb_tournament_leaderboard.turned_over >= tbdb_tournament.start_currency AS qualified'))

		->join(
			'tbdb_tournament_leaderboard', 'tbdb_tournament_leaderboard.tournament_id', '=', 'tbdb_tournament.id'
		)->where(
			'tbdb_tournament_leaderboard.tournament_id', '=', $tournamentId
			)
//		->whereRaw('tbdb_tournament_leaderboard.turned_over >= tbdb_tournament.start_currency')
		->orderBy('tbdb_tournament_leaderboard.currency', 'DESC')
		->orderBy('qualified', 'DESC')
		->get()->toArray();

//		return Tournament::select(\DB::raw('*, tbdb_tournament_leaderboard.turned_over >= tbdb_tournament.start_currency AS qualified'))->join('tbdb_tournament_leaderboard', 'tbdb_tournament_leaderboard.tournament_id', '=', 'tbdb_tournament.id')->where('tbdb_tournament_leaderboard.tournament_id', '=', $tournamentId)->orderBy('tbdb_tournament_leaderboard.currency', 'DESC')->orderBy('qualified', 'DESC')->get()->toArray();

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
				if (array_get($row, 'qualified', '0') === '1') {
					$rank['position'] = $previousRank;
				} else {
					$rank['position'] = '';
				}

				break;
			}
		}

		$rank['total_entrants'] = $this->getLeaderboardCount($tournamentId);
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