<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/04/2015
 * Time: 12:40 PM
 */
namespace TopBetta\Repositories\Contracts;

interface TournamentTicketBuyInHistoryRepositoryInterface
{
    public function getByTicketAndType($ticketId, $typeId);

    public function getTotalByTicketAndType($ticketId, $typeId);
}