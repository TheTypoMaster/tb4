<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/04/2015
 * Time: 6:48 PM
 */

namespace TopBetta\Services\Tournaments;


use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentBuyInRepositoryInterface;
use TopBetta\Repositories\DbTournamentRepository;
use Carbon\Carbon;

class TournamentService {

    /**
     * @var DbTournamentRepository
     */
    private $tournamentRepository;
    /**
     * @var TournamentBuyInRepositoryInterface
     */
    private $buyInRepository;
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;

    public function __construct(DbTournamentRepository $tournamentRepository,
                                TournamentBuyInRepositoryInterface $buyInRepository,
                                CompetitionRepositoryInterface $competitionRepository)
    {
        $this->tournamentRepository = $tournamentRepository;
        $this->buyInRepository = $buyInRepository;
        $this->competitionRepository = $competitionRepository;
    }

    public function createTournament($tournamentData)
    {
        //get tournament name and desc
        $tournamentData['name'] = $this->generateTournamentAutomatedText('name', $tournamentData);
        $tournamentData['description'] = $this->generateTournamentAutomatedText('description', $tournamentData);

        //convert from cents
        $tournamentData['start_currency'] *= 100;
        $tournamentData['minimum_prize_pool'] *= 100;

        //tournament buy in
        if( $buyinId = array_get($tournamentData, 'tournament_buyin_id') ) {
            $buyin = $this->buyInRepository->find($buyinId);

            if( $buyin ) {
                $tournamentData['buy_in'] = $buyin->buy_in;
                $tournamentData['entry_fee'] = $buyin->entry_fee;
            }
        }

        //get start and end dates
        if( $eventGroupId = array_get($tournamentData, 'event_group_id', null)) {
            if ($event = $this->competitionRepository->getFirstEventForCompetition($eventGroupId)) {
                $tournamentData['start_date'] = $event->start_date;
                $tournamentData['end_date']   = $this->competitionRepository->getLastEventForCompetition($eventGroupId)->start_date;
            } else {
                if( $eventGroup = $this->competitionRepository->find($eventGroupId) ) {
                    $tournamentData['start_date'] = $eventGroup->find($eventGroup)->start_date;
                    $tournamentData['end_date']   = $eventGroup->competitionRepository->find($eventGroup)->start_date;
                }
            }
            //betting closed date
            if (array_get($tournamentData, 'close_betting_on_first_match_flag')) {
                $tournamentData['betting_closed_date'] = $tournamentData['start_date'];
            } else {
                $tournamentData['betting_closed_date'] = $tournamentData['end_date'];
            }
        }

        //tournament of the day
        $tod = array_get($tournamentData, 'tod_flag', null);
        if ( $tod && $this->tournamentRepository->tournamentOfTheDay($tod, Carbon::createFromFormat('Y-m-d H:i:s', $tournamentData['start_date'])->toDateString()) ) {
            throw new \Exception("Tournament of the day already exists");
        } else if ( ! $tod ) {
            $tournamentData['tod_flag'] = '';
        }

        //rebuy data
        if ( array_get($tournamentData, 'rebuys', null) ) {
            $buyin = $this->buyInRepository->find(array_get($tournamentData, 'tournament_rebuy_buyin_id'));

            if( $buyin ) {
                $tournamentData['rebuy_buyin'] = $buyin->buy_in * 100;
                $tournamentData['rebuy_entry'] = $buyin->entry_fee * 100;
            }
        }

        //topup data
        if ( array_get($tournamentData, 'topup_flag', null)) {
            $buyin = $this->buyInRepository->find(array_get($tournamentData, 'tournament_topup_buyin_id'));

            if( $buyin ) {
                $tournamentData['topup_buyin'] = $buyin->buy_in * 100;
                $tournamentData['topup_entry'] = $buyin->entry_fee * 100;
            }
        }

        $tournament = $this->tournamentRepository->create(array_except($tournamentData, array(
            'tournament_buyin_id',
            'tournament_topup_buyin_id',
            'tournament_rebuy_buyin_id',
            'tournament_labels',
        )));

        $tournament = $this->tournamentRepository->find($tournament['id']);

        //add labels
        if( $labels = array_get($tournamentData, 'tournament_labels') ) {
            $tournament->tournamentlabels()->sync($labels);
        }

        return $tournament;
    }

    /**
     * Borrowed from legacy administrator
     * @param $field
     * @return string
     */
    private function generateTournamentAutomatedText($field, $tournamentData)
    {
        $jackpot_flag			= array_get($tournamentData, 'jackpot_flag', 0);
        $parent_tournament_id	= array_get($tournamentData, 'parent_tournament_id', null);
        $minimum_prize_pool		= array_get($tournamentData, 'minimum_prize_pool', 0);


        $reinvest_winnings_flag = array_get($tournamentData, 'reinvest_winnings_flag', 0);
        $closed_betting_on_first_match_flag = array_get($tournamentData, 'closed_betting_on_first_match_flag', 0);
        $tournament_sponsor_name = array_get($tournamentData, 'tournament_sponsor_name', null);


        $buyin_amount				= number_format(array_get($tournamentData, 'buy_in', 0)/100, 2);
        $minimum_prize_pool_amount	= number_format($minimum_prize_pool, 2);
        $free_credit_flag 			= (int)array_get($tournamentData, 'free_credit_flag', 0);

        $automated_text = '';
        $tournamntType = '';

        switch ($field) {
            case 'name':
                $meeting_id				= array_get($tournamentData, 'event_group_id', -1);
                $event_id				= array_get($tournamentData, 'event_id', -1);
                $future_meeting_venue	= array_get($tournamentData, 'future_meeting_venue', -1);

                if (!empty($meeting_id) && $meeting_id != -1) {
                    $meeting	= $this->competitionRepository->find($meeting_id);
                    $automated_text	.= $meeting->name ;
                } else if (!empty($future_meeting_venue) && $future_meeting_venue != -1) {
                    $automated_text .= $future_meeting_venue;
                }
                // $automated_text .= ($buyin->buy_in > 0 ? ' $' . $buyin_amount : ' FREE');

                //	if (!$jackpot_flag) {
                //		$automated_text .= '/' . $minimum_prize_pool_amount;
                //	}

                break;
            case 'description':
                if ($jackpot_flag) {
                    $tournamntType = 'jackpot';
                } elseif ($free_credit_flag){
                    $tournamntType = 'free credit';
                }else {
                    $tournamntType = 'cash';
                }
                $automated_text  = 'This is a ' . $tournamntType . ' tournament.';
                $automated_text .= ' The cost of entry is ';

                if ($buyin_amount > 0) {
                    $automated_text .= '$' . $buyin_amount . ' + $' . number_format(array_get($tournamentData, 'entry_fee',0)/100, 2) . '.';
                } else {
                    $automated_text .= 'Free.';

                }

                if ($closed_betting_on_first_match_flag == 1){
                    $automated_text .= ' You can not bet after the 1st event in this tournament starts.';
                }

                if ($reinvest_winnings_flag == 0 && $closed_betting_on_first_match_flag != 1){
                    $automated_text .= ' You can not re-invest your winnings in this tournament.';
                }

                $automated_text .= ' Winners will receive';

                if (empty($jackpot_flag) || -1 == $parent_tournament_id) {
                    $automated_text .= ' a share of a guaranteed $' . $minimum_prize_pool_amount;
                    if($free_credit_flag){
                        $automated_text .= ' in free credit.';
                    } else {
                        $automated_text .= '.';
                    }

                    if ($buyin_amount > 0) {
                        $automated_text .= ' Once the minimum is reached, the prize pool will continue to grow by $' . $buyin_amount . ' per entrant.';
                    }
                } else {
                    $parent_tournament	= $this->tournamentRepository->find($parent_tournament_id);
                    $start_date_time	= strtotime($parent_tournament->start_date);

                    $automated_text .= ' a ticket into the ' . $parent_tournament->name;
                    $automated_text .= ' tournament on ' . date('D', $start_date_time) . ' ' . date('jS F', $start_date_time) . '.';

                    if ($buyin_amount == 0) {

                        $ticket_count	= floor($minimum_prize_pool * 100 / ($parent_tournament->entry_fee + $parent_tournament->buy_in));

                        if($ticket_count > 1) {
                            $automated_text .= ' There are ' . $ticket_count . ' tickets to be won.';
                        } else {
                            $automated_text .= ' There is ' . $ticket_count . ' ticket to be won.';
                        }
                    } else {
                        $automated_text .= ' The Number of tickets awarded will depend on the number of entrants.';
                    }
                }

                $automated_text .= "\n\nGood luck and good punting!";

                break;
        }

        return $automated_text;
    }
}