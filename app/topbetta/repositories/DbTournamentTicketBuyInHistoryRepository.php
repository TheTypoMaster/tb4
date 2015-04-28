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
}