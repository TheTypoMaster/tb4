<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 10/08/2015
 * Time: 3:48 PM
 */
namespace TopBetta\Repositories\Contracts;

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
}