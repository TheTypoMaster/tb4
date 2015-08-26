<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 13/08/2015
 * Time: 9:18 AM
 */

namespace TopBetta\Services\Tournaments\Email;


use TopBetta\Services\Tournaments\Resulting\TournamentResult;

class WinnerNotificationEmail implements TournamentEmail {

    /**
     * @var \TopBetta\Services\Tournaments\Resulting\TournamentResult
     */
    private $result;

    public function getBody()
    {
        $body = '';

        $params = array(
            "amount" => '$' . number_format($this->result->getAmount()/100, 2),
        );

        if ($parentTournament = $this->result->getJackpotTicket()) {

            $params['parent_tournament'] = $parentTournament->name;
            $params['tourn_amount'] = '$' . number_format(($parentTournament->entry_fee + $parentTournament->buy_in)/100, 2);

            if ($this->result->jackpotTicketExists()) {
                $body = \Lang::get($this->result->getAmount() ? 'tournaments.ticket_already_registered' : 'tournaments.ticket_already_registerd_cash', $params);
            } else {
                $body = \Lang::get($this->result->getAmount() ? "tournaments.ticket_cash" : "tournaments.ticket_only", $params);
            }
        } else {
            if ($freeCredit = $this->result->getFreeCreditAmount()) {
                $params['amount'] = '$' . number_format($this->result->getFreeCreditAmount()/100, 2);
                $body = \Lang::get('tournaments.free_credit', $params);
            } else {
                $body = \Lang::get('tournaments.cash_only', $params);
            }
        }

        return $body;
    }

    /**
     * @param \TopBetta\Services\Tournaments\Resulting\TournamentResult $result
     * @return $this
     */
    public function setResult(TournamentResult $result)
    {
        $this->result = $result;
        return $this;
    }
}