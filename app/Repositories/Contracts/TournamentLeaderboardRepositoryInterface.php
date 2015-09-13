<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/07/2015
 * Time: 12:31 PM
 */
namespace TopBetta\Repositories\Contracts;

use DB;

interface TournamentLeaderboardRepositoryInterface
{
    /**
     * getTournamentLeaderboard get the current tournament leaderboard
     * @param $tournamentID
     * @param int $limit
     * @param $startCurrency
     * @param bool $qualified
     * @return mixed
     */
    public function getTournamentLeaderboard($tournamentID, $limit = 50, $qualified = false);

    public function getTournamentLeaderboardCollection($tournamentID, $limit = 50, $qualified = false);

    public function getAllLeaderboardRecordsForTournament($tournament);

    public function getLeaderboardRecordForUserInTournament($userId, $tournamentId);

    public function updateLeaderboardRecordForUserInTournament($userId, $tournamentId, $turnover, $currency);

    public function getLeaderBoardPositionForUser($userId, $tournamentId, $qualified = true);

    public function getLeaderboardRecordsForTournamentWithCurrencyGreaterThen($tournamentId, $currency, $onlyQualified = true);

    public function getTournamentLeaderboardPaginated($tournamentID, $limit = 50);
}