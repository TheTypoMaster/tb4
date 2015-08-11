<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/08/2015
 * Time: 3:34 PM
 */

namespace TopBetta\Services\Tournaments\Resulting;


class TournamentTicketPrize implements TournamentPrize {

    /**
     * @var
     */
    private $ticket;
    /**
     * @var
     */
    private $jackpotTicket;

    public function payout(){}

    /**
     * @return mixed
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param mixed $ticket
     * @return $this
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJackpotTicket()
    {
        return $this->jackpotTicket;
    }

    /**
     * @param mixed $jackpotTicket
     * @return $this
     */
    public function setJackpotTicket($jackpotTicket)
    {
        $this->jackpotTicket = $jackpotTicket;
        return $this;
    }
}