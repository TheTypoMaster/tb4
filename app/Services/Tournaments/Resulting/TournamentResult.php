<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/08/2015
 * Time: 8:53 AM
 */

namespace TopBetta\Services\Tournaments\Resulting;


use TopBetta\Models\TournamentTicketModel;

class TournamentResult {

    private $ticket;

    private $amount = 0;

    private $freeCreditAmount = 0;

    private $jackpotTicket = null;

    public function __construct(TournamentTicketModel $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * @return TournamentTicketModel
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param TournamentTicketModel $ticket
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
    public function getFreeCreditAmount()
    {
        return $this->freeCreditAmount;
    }

    /**
     * @param mixed $freeCreditAmount
     * @return $this
     */
    public function setFreeCreditAmount($freeCreditAmount)
    {
        $this->freeCreditAmount = $freeCreditAmount;
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