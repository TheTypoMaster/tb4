<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 10/08/2015
 * Time: 3:42 PM
 */

namespace TopBetta\Services\Tournaments\Resulting;


class TournamentResult {

    /**
     * @var \TopBetta\Models\TournamentTicketModel
     */
    private $ticket;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var int
     */
    private $jackpotTournament;

    /**
     * @return \TopBetta\Models\TournamentTicketModel
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
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJackpotTournament()
    {
        return $this->jackpotTournament;
    }

    /**
     * @param mixed $jackpotTournament
     * @return $this
     */
    public function setJackpotTournament($jackpotTournament)
    {
        $this->jackpotTournament = $jackpotTournament;
        return $this;
    }
}