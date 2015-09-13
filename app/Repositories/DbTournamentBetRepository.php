<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 16/08/2014
 * File creation time: 11:58 PM
 * Project: tb4
 */

use TopBetta\Models\TournamentBetModel;
use TopBetta\Repositories\Contracts\TournamentBetRepositoryInterface;

class DbTournamentBetRepository extends BaseEloquentRepository implements TournamentBetRepositoryInterface
{

    protected $model;

    public function __construct(TournamentBetModel $tournamentbets){
        $this->model = $tournamentbets;
    }

    /**
     * @param $ticketId
     * @return mixed
     */
    public function getResultedUserBetsInTournament($ticketId){
        return $this->model->where('tournament_ticket_id', $ticketId)
                            ->get();
                           // ->where('bet_result_status_id', $status)->get();
    }

    public function getBetsForUserInTournamentWhereEventStatusIn($user, $tournament, $eventStatuses)
    {
        return $this->model
            ->join('tbdb_tournament_ticket', 'tbdb_tournament_ticket.id', '=', 'tbdb_tournament_bet.tournament_ticket_id')
            ->join('tbdb_tournament_bet_selection', 'tbdb_tournament_bet_selection.tournament_bet_id', '=', 'tbdb_tournament_bet.id')
            ->join('tbdb_selection', 'tbdb_tournament_bet_selection.selection_id', '=', 'tbdb_selection.id')
            ->join('tbdb_market', 'tbdb_market.id', '=', 'tbdb_selection.market_id')
            ->join('tbdb_event', 'tbdb_event.id', '=', 'tbdb_market.event_id')
            ->where('tbdb_tournament_ticket.user_id', $user)
            ->where('tbdb_tournament_ticket.tournament_id', $tournament)
            ->whereIn('tbdb_event.event_status_id', $eventStatuses)
            ->groupBy('tbdb_tournament_bet.id')
            ->with('betType', 'selections', 'selections.market.event.competition.sport', 'selections.market.markettype', 'selections.result', 'selections.price')
            ->get(array("tbdb_tournament_bet.*"));
    }

    public function getBetResourcesForUserInTournamentWhereEventStatusIn($user, $tournament, $eventStatuses)
    {
        return $this->getBetBuilder()
            ->join('tbdb_tournament_ticket', 'tbdb_tournament_ticket.id', '=', 'tb.tournament_ticket_id')
            ->where('tbdb_tournament_ticket.tournament_id', $tournament)
            ->whereIn('e.event_status_id', $eventStatuses)
            ->get(array("tbdb_tournament_bet.*"));
    }

    public function getBetsForEventByStatusIn($eventId, $status, $betType = null)
    {
        $model = $this->model
            ->join('tbdb_tournament_bet_selection', 'tbdb_tournament_bet_selection.tournament_bet_id', '=', 'tbdb_tournament_bet.id')
            ->join('tbdb_selection', 'tbdb_tournament_bet_selection.selection_id', '=', 'tbdb_selection.id')
            ->join('tbdb_market', 'tbdb_selection.market_id', '=', 'tbdb_market.id')
            ->join('tbdb_event', 'tbdb_market.event_id', '=', 'tbdb_event.id')
            ->where('tbdb_event.id', '=', $eventId)
            ->where('tbdb_tournament_bet.bet_result_status_id', $status)
            ->where('tbdb_tournament_bet.bet_product_id', $product);

        if( $betType ) {
            $model->where('tbdb_tournament_bet.bet_type_id', $betType);
        }

        return $model->get(array('tbdb_tournament_bet.*'));
    }

    public function getBetsForEventByStatus($eventId, $status, $betType = null)
    {
        $model = $this->model
            ->join('tbdb_tournament_bet_selection', 'tbdb_tournament_bet_selection.tournament_bet_id', '=', 'tbdb_tournament_bet.id')
            ->join('tbdb_selection', 'tbdb_tournament_bet_selection.selection_id', '=', 'tbdb_selection.id')
            ->join('tbdb_market', 'tbdb_selection.market_id', '=', 'tbdb_market.id')
            ->join('tbdb_event', 'tbdb_market.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_bet_result_status', 'tbdb_bet_result_status.id', '=', 'tbdb_tournament_bet.bet_result_status_id')
            ->where('tbdb_event.id', '=', $eventId)
            ->where('tbdb_bet_result_status.name', $status);

        if( $betType ) {
            $model->where('tbdb_tournament_bet.bet_type_id', $betType);
        }

        return $model->get(array('tbdb_tournament_bet.*'));
    }

    public function getBetsForSelection($selectionId)
    {
        return $this->model
            ->join('tbdb_tournament_bet_selection', 'tbdb_tournament_bet_selection.tournament_bet_id', '=', 'tbdb_tournament_bet.id')
            ->join('tbdb_selection', 'tbdb_tournament_bet_selection.selection_id', '=', 'tbdb_selection.id')
            ->where('tbdb_selection.id', $selectionId)
            ->where('tbdb_tournament_bet.resulted_flag', false)
            ->get(array("tbdb_tournament_bet.*"));
    }

    public function getBetsForMarket($marketId)
    {
        return $this->model
            ->join('tbdb_tournament_bet_selection', 'tbdb_tournament_bet_selection.tournament_bet_id', '=', 'tbdb_tournament_bet.id')
            ->join('tbdb_selection', 'tbdb_tournament_bet_selection.selection_id', '=', 'tbdb_selection.id')
            ->join('tbdb_market', 'tbdb_market.id', '=', 'tbdb_selection.market_id')
            ->where('tbdb_market.id', $marketId)
            ->where('tbdb_tournament_bet.resulted_flag', false)
            ->get(array("tbdb_tournament_bet.*"));
    }

    public function getBetsOnEventForTicket($ticket, $event)
    {
        return $this->model
            ->join('tbdb_tournament_bet_selection as bs', 'bs.tournament_bet_id', '=', 'tbdb_tournament_bet.id')
            ->join('tbdb_selection as s', 's.id', '=', 'bs.selection_id')
            ->join('tbdb_market as m', 'm.id', '=', 's.market_id')
            ->join('tbdb_event as e', 'e.id', '=', 'm.event_id')
            ->where('tbdb_tournament_bet.tournament_ticket_id', $ticket)
            ->where('e.id', $event)
            ->groupBy('tbdb_tournament_bet.id')
            ->get(array('tbdb_tournament_bet.*'));
    }

    public function getBetsForUserTournament($user, $tournament)
    {
        return $this->getBetBuilder()
            ->join('tbdb_tournament_ticket as tt', 'tt.id', '=', 'tb.tournament_ticket_id')
            ->where('tt.user_id', $user)
            ->where('tt.tournament_id', $tournament)
            ->get();
    }

    public function findBets($bets)
    {
        return $this->getBetBuilder()
            ->whereIn('tb.id', $bets)
            ->get();
    }

    protected function getBetBuilder()
    {
        return $this->model
            ->from('tbdb_tournament_bet as tb')
            ->join('tbdb_bet_type as bt', 'bt.id', '=', 'tb.bet_type_id')
            ->leftJoin('tbdb_bet_result_status as brs', 'brs.id', '=', 'tb.bet_result_status_id')
            ->join('tbdb_tournament_bet_selection as bs', 'bs.tournament_bet_id', '=', 'tb.id')
            ->join('tbdb_selection as s ', 's.id', '=', 'bs.selection_id')
            ->leftJoin('tbdb_selection_price as sp', function ($q) {
                $q->on('sp.selection_id', '=', 's.id')
                    ->on('sp.bet_product_id', '=', 'tb.bet_product_id');
            })
            ->leftJoin('tbdb_selection_result as sr', 'sr.selection_id', '=', 's.id')
            ->join('tbdb_market as m', 'm.id', '=', 's.market_id')
            ->join('tbdb_market_type as mt', 'mt.id', '=', 'm.market_type_id')
            ->join('tbdb_event as e', 'e.id', '=', 'm.event_id')
            ->join('tbdb_event_group_event as ege', 'ege.event_id', '=', 'e.id')
            ->join('tbdb_event_group as eg', 'eg.id', '=', 'ege.event_group_id')
            ->join('tbdb_bet_product as bp', 'bp.id', '=', 'tb.bet_product_id')
            ->leftJoin('tb_product_provider_match as ppm', 'ppm.tb_product_id', '=', 'bp.id')
            ->groupBy('tb.id')
            ->select(array(
                'tb.id', 'tb.win_amount', 'tb.fixed_odds', 's.id as selection_id', 's.name as selection_name', 'm.id as market_id', 'mt.name as market_name',
                'e.id as event_id', 'e.name as event_name', 'eg.id as competition_id', 'eg.name as competition_name', 'brs.name as status',
                'bt.name as bet_type', 'e.start_date as start_date', 'eg.type_code as event_type', 'sp.win_odds as win_odds', 'sp.place_odds as place_odds',
                'sr.win_dividend', 'sr.place_dividend', 's.number as selection_number', 'bp.is_fixed_odds as fixed',
                'ppm.provider_product_name', 'bp.id as product_id',
            ));
    }

} 