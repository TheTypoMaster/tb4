<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/04/2015
 * Time: 4:09 PM
 */

namespace TopBetta\Services\Tournaments;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\TournamentBuyInTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Repositories\DbTournamentLeaderboardRepository;
use TopBetta\Services\Tournaments\Exceptions\TournamentEntryException;

class TournamentLeaderboardService {

    /**
     * @var DbTournamentLeaderboardRepository
     */
    private $leaderboardRepository;
    /**
     * @var TournamentBuyInTypeRepositoryInterface
     */
    private $buyInTypeRepository;
    /**
     * @var TournamentTicketRepositoryInterface
     */
    private $ticketRepository;

    public function __construct(DbTournamentLeaderboardRepository $leaderboardRepository, TournamentBuyInTypeRepositoryInterface $buyInTypeRepository, TournamentTicketRepositoryInterface $ticketRepository)
    {
        $this->leaderboardRepository = $leaderboardRepository;
        $this->buyInTypeRepository = $buyInTypeRepository;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Creates a leaderboard record for the user
     * @param $tournament
     * @param $user
     * @return mixed
     * @throws TournamentEntryException
     */
    public function createLeaderboardRecordForUser($tournament, $user)
    {
        $leaderboard = $this->leaderboardRepository->create(array(
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
            'currency' => $tournament->start_currency,
            'turned_over' => 0,
            'updated_date' => Carbon::now()->toDateTimeString(),
        ));

        if ( ! $leaderboard ) {
            throw new TournamentEntryException("Error creating leaderboard");
        }

        return $leaderboard;
    }

    public function increaseCurrency($leaderboardId, $amount, $addBalanceToTurnOver = false)
    {
        $leaderboard = $this->leaderboardRepository->find($leaderboardId);

        $data = array("currency" => $leaderboard->currency + $amount);

        //add the balance to needed turnover?
        if( $addBalanceToTurnOver ){
            $data['balance_to_turnover'] = $leaderboard->balance_to_turnover + $amount;
        }

        return $this->leaderboardRepository->updateWithId($leaderboardId, $data);
    }

    public function decreaseCurrency($leaderboardId, $amount)
    {
        return $this->increaseCurrency($leaderboardId, -$amount);
    }

    public function getLeaderboard($tournamentId, $limit = 50, $onlyQualified = false)
    {
        $rebuyId = $this->buyInTypeRepository->getIdByKeyword(TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_REBUY);
        $topupId = $this->buyInTypeRepository->getIdByKeyword(TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_TOPUP);

        $leaderboard = $this->leaderboardRepository->getTournamentLeaderboard($tournamentId, $rebuyId, $topupId, $limit, true);

        if( ! $onlyQualified && count($leaderboard) < $limit) {
            $leaderboard = array_merge(
                $leaderboard,
                $this->leaderboardRepository->getTournamentLeaderboard($tournamentId, $rebuyId, $topupId, $limit, false)
            );
        }

        //get the ranks for users
        $leaderboardArray = array();
        $position = 1;
        $count = 0;
        $amount = 0;
        foreach($leaderboard as $record)
        {
            $leaderboardRecord = array(
                'id' => $record['id'],
                'username' => $record['username'],
                'currency' => $record['currency'],
                'qualified' => $record['qualified'],
                'turned_over' => $record['turned_over'],
            );

            if($record['qualified']) {
                if ($record['currency'] < $amount) {
                    $position += $count;
                    $count = 0;
                }

                $count++;
                $leaderboardRecord['rank'] = $position;
                $amount = $record['currency'];
            } else {
                $leaderboardRecord['rank'] = '-';
            }

            $leaderboardArray[] = $leaderboardRecord;
        }

        return $leaderboardArray;
    }

    public function getLeaderboardRecordWithPositionForUser($user, $tournament)
    {
        $leaderboardRecord = $this->leaderboardRepository->getLeaderboardRecordForUserInTournament($user, $tournament);

        $position = 0;
        if( $leaderboardRecord->balance_to_turnover <= $leaderboardRecord->turned_over ) {
            $position = $this->leaderboardRepository->getLeaderboardRecordsForTournamentWithCurrencyGreaterThen($tournament, $leaderboardRecord->currency)->count() + 1;
        }

        return array('leaderboard' => $leaderboardRecord, "position" => $position);
    }


}