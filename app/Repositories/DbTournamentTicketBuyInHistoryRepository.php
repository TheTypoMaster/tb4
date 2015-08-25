<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/04/2015
 * Time: 12:37 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\TournamentTicketBuyInHistoryModel;
use TopBetta\Repositories\Contracts\TournamentTicketBuyInHistoryRepositoryInterface;

class DbTournamentTicketBuyInHistoryRepository extends BaseEloquentRepository implements TournamentTicketBuyInHistoryRepositoryInterface
{

    public function __construct(TournamentTicketBuyInHistoryModel $model)
    {
        $this->model = $model;
    }

    public function getByTicketAndType($ticketId, $typeId)
    {
        return $this->model->where('tournament_ticket_id', $ticketId)->where('tournament_buyin_type_id', $typeId)->get();
    }

    public function getTotalByTicketAndType($ticketId, $typeId)
    {
        return $this->model->where('tournament_ticket_id', $ticketId)->where('tournament_buyin_type_id', $typeId)->count();
    }

    public function getByBuyinTransaction($transaction)
    {
        return $this->model
            ->where('buyin_transaction_id', $transaction)
            ->first();
    }

    public function getByEntryTransaction($transaction)
    {
        return $this->model
            ->where('entry_transaction_id', $transaction)
            ->first();
    }
}