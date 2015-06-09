<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/06/2015
 * Time: 12:11 PM
 */

namespace TopBetta\Services\Betting\BetResults;

use Carbon\Carbon;
use Log;
use TopBetta\Models\TournamentBetModel;
use TopBetta\Repositories\Contracts\BetResultStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentBetRepositoryInterface;
use TopBetta\Repositories\DbTournamentLeaderboardRepository;
use TopBetta\Services\Betting\BetDividend\BetDividendService;
use TopBetta\Services\Betting\EventService;

class TournamentBetResultService {

    /**
     * @var EventService
     */
    private $eventService;
    /**
     * @var TournamentBetRepositoryInterface
     */
    private $betRepositoryInterface;
    /**
     * @var BetTypeRepositoryInterface
     */
    private $betTypeRepository;
    /**
     * @var BetResultStatusRepositoryInterface
     */
    private $betResultStatusRepository;
    /**
     * @var BetDividendService
     */
    private $betDividendService;
    /**
     * @var DbTournamentLeaderboardRepository
     */
    private $leaderboardRepository;


    public function __construct(EventService $eventService,
                                TournamentBetRepositoryInterface $betRepositoryInterface,
                                BetTypeRepositoryInterface $betTypeRepository,
                                BetResultStatusRepositoryInterface $betResultStatusRepository,
                                BetDividendService $betDividendService,
                                DbTournamentLeaderboardRepository $leaderboardRepository)
    {
        $this->eventService = $eventService;
        $this->betRepositoryInterface = $betRepositoryInterface;
        $this->betTypeRepository = $betTypeRepository;
        $this->betResultStatusRepository = $betResultStatusRepository;
        $this->betDividendService = $betDividendService;
        $this->leaderboardRepository = $leaderboardRepository;
    }

    /**
     * Results all tournament bets for an event
     * @param $event
     * @return array
     * @throws \Exception
     */
    public function resultAllBetsForEvent($event)
    {
        $interim = $this->eventService->isEventInterim($event);

        $betType = null;
        //bet type is interim so only get win bets
        if( $interim ) {
            $betType = $this->betTypeRepository->getBetTypeByName(BetTypeRepositoryInterface::TYPE_WIN);
        } else if ( ! $this->eventService->isEventPaying($event) ) {
            //not paying and not interim so bad state
            throw new \Exception("Event in invalid state");
        }

        //get all tournament bets
        $bets = $this->betRepositoryInterface->getBetsForEventByStatusIn(
            $event->id,
            $this->betResultStatusRepository->getByName(BetResultStatusRepositoryInterface::RESULT_STATUS_UNRESULTED)->id,
            $betType ? $betType->id : null
        );

        //result all bets
        $results = $this->resultBets($bets, $interim);

        //set paid flag if paying results
        if( ! $interim ) {
            $this->eventService->setEventPaid($event);
        }

        return $results;
    }

    /**
     * Result all given tournament bets
     * @param $bets
     * @param bool $interim
     * @return array
     */
    public function resultBets($bets, $interim = false)
    {
        $betArray = array();

        foreach($bets as $bet) {
            $betArray[] = $this->resultBet($bet, $interim);
        }

        return $betArray;
    }

    /**
     * Gets the dividend for a bet and updates the leaderboard record and bet record
     * @param TournamentBetModel $bet
     * @param bool $interim
     * @return TournamentBetModel
     */
    public function resultBet(TournamentBetModel $bet, $interim = false)
    {
        Log::info("RESULTING TOURNAMENT BET " . $bet->id);

        //get the dividend
        $dividend = $this->betDividendService->getResultedDividendForBet($bet);

        //calculate win amount
        $amount = $this->calculateBetWin($bet, $dividend);

        //If win amount update the bet record
        if( $amount ) {
            Log::info("WINNING BET " . $bet->id . " AMOUNT " . $amount/100);
            $bet->win_amount = $amount;
        }

        //set the bet to resulted if it is winning or event is paying
        if( ! $interim || $amount ) {
            $this->updateLeaderboardCurrency($bet, $amount);
            $bet->resulted_flag = true;
            $bet->bet_result_status_id = $this->betResultStatusRepository->getByName(BetResultStatusRepositoryInterface::RESULT_STATUS_PAID)->id;
        }

        //set updated date
        if( $bet->isDirty() ) {
            $bet->updated_date = Carbon::now();
        }

        $bet->save();

        return $bet;
    }

    /**
     * Updates the leaderboard record with bet results //TODO: Abstract to tournament leaderboard service
     * @param $bet
     * @param $amount
     * @return mixed
     */
    public function updateLeaderboardCurrency($bet, $amount)
    {
        $leaderboard = $this->leaderboardRepository->getLeaderboardRecordForUserInTournament($bet->ticket->user_id, $bet->ticket->tournament_id);

        return $this->leaderboardRepository->updateWithId($leaderboard->id, array(
            "currency" => $leaderboard->currency + $amount - $bet->bet_amount,
        ));
    }

    /**
     * Calculates the amount won given a bet and the dividend for the bet.
     * @param $bet
     * @param $dividend
     * @return string
     */
    public function calculateBetWin($bet, $dividend)
    {
        return bcmul($bet->bet_amount, $dividend, 2);
    }
}