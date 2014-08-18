<?php namespace TopBetta\Tournaments;
/**
 * Coded by Oliver Shanahan
 * File creation date: 16/08/2014
 * File creation time: 11:38 PM
 * Project: tb4
 */

use Log;

use TopBetta\Repositories\DbTournamentRepository;
use TopBetta\Repositories\DbTournamentLeaderboardRepository;
use TopBetta\Repositories\DbTournamentTicketRepository;
use TopBetta\Repositories\DbTournamentBetRepository;
use TopBetta\Repositories\DbTournamentBetSelectionRepository;
use TopBetta\Repositories\DbTournamentSelectionResultRepository;


class TournamentReprocess {

    protected $tournaments;
    protected $tournamentleaderboards;
    protected $tournamenttickets;
    protected $tournamentbets;
    protected $tournamentselectionresults;

    public function __construct(DbTournamentRepository $tournaments,
                                DbTournamentLeaderboardRepository $tournamentleaderboards,
                                DbTournamentTicketRepository $tournamenttickets,
                                DbTournamentBetRepository $tournamentbets,
                                DbTournamentBetSelectionRepository $tournamentbetselections,
                                DbTournamentSelectionResultRepository $tournamentselectionresults){
        $this->tournaments = $tournaments;
        $this->tournamentleaderboards = $tournamentleaderboards;
        $this->tournamenttickets = $tournamenttickets;
        $this->tournamentbets = $tournamentbets;
        $this->tournamentbetselections = $tournamentbetselections;
        $this->tournamentselectionresults = $tournamentselectionresults;
    }

    public function reprocessTournamentbets($tournamentId){

        // get tournament model // TODO try catch etc...
        $tournament = $this->tournaments->find($tournamentId);

        Log::debug('Tournament Reprocess - ID: '. $tournamentId. ', Name: '.$tournament->name.', Starting Bucks: '.$tournament->start_currency);

        // get the tickets for users in the tournament and loop on them
        $tournamentTickets = $this->tournamenttickets->getTicketsInTournament($tournament->id);

        Log::debug('Tournament Reprocess - Tickets Found: '. count($tournamentTickets));

        // loop on the tickets in the tournament
        foreach($tournamentTickets as $ticket){

            // get the bets for each ticket/user in the tournament
            $ticketBets = $this->tournamentbets->getResultedUserBetsInTournament($ticket->id);
            Log::debug('Tournament Reprocess - Ticket ID: '.$ticket->id.', Bets Found: '. count($ticketBets));

            $currency = $tournament->start_currency;
            $turnover = 0;
            // loop on each bet
            foreach($ticketBets as $bet){

                Log::debug('Tournament Reprocess - Ticket ID: '.$ticket->id.', Processing Bet Id: '. $bet->id);

                // get the bets slection id
                $betSelectionId = $this->tournamentbetselections->getBetSelectionId($bet->id);

                // bet's that have been procesed already
                if($bet->bet_result_status_id == 2){

                    // check if the bet is a winner
                    $winningBet = $this->tournamentselectionresults->getSelectionResultForSelectionId($betSelectionId);

                    // if it is add the win_amount to the bet model
                    if($winningBet){
                        $bet->win_amount = $bet->bet_amount * $bet->fixed_odds;
                        Log::debug('Tournament Reprocess - Ticket ID: '.$ticket->id.', Processing Bet Id: '. $bet->id. ',  Winning Bet Found');
                    }else{
                        $bet->win_amount = 0;
                    }

                    // minus the bet amount from the currency
                    $currency -= $bet->bet_amount;

                    // add the win amount to the currency
                    $currency += $bet->win_amount;

                }

                // add the bet amount to the turnover
                $turnover += $bet->bet_amount;

                Log::debug('Tournament Reprocess - Ticket ID: '.$ticket->id.', Processing Bet Id: '. $bet->id. ', BS: '.$bet->bet_result_status_id.', WA: '. $bet->win_amount.', BA: '. $bet->bet_amount.', FO: '.$bet->fixed_odds. ', TO: '.$turnover.', C: '.$currency);
                // save the bet
                $bet->save();
            }
            // update the leaderboard record
            $leaderboarModel = $this->tournamentleaderboards-> updateLeaderboardRecordForUserInTournament($ticket->user_id, $tournamentId, $turnover, $currency);
            Log::debug('Tournament Reprocess - Ticket ID: '.$ticket->id.', Leaderboard Updated: '.print_r($leaderboarModel, true));

        }
    }
}