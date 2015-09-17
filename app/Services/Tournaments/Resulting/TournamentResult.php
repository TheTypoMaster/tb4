<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/08/2015
 * Time: 8:53 AM
 */

namespace TopBetta\Services\Tournaments\Resulting;


use Illuminate\Contracts\Support\Arrayable;
use TopBetta\Models\TournamentTicketModel;

class TournamentResult implements Arrayable {

    private $ticket;

    private $amount = 0;

    private $freeCreditAmount = 0;

    private $jackpotTicket = null;

    private $jackpotTicketExists = false;

    private $position;

    public function __construct(TournamentTicketModel $ticket = null)
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

    /**
     * @return boolean
     */
    public function jackpotTicketExists()
    {
        return $this->jackpotTicketExists;
    }

    /**
     * @param boolean $jackpotTicketExists
     * @return $this
     */
    public function setJackpotTicketExists($jackpotTicketExists)
    {
        $this->jackpotTicketExists = $jackpotTicketExists;
        return $this;
    }

    public function getTotalFreeCreditAmount()
    {
        $amount = $this->getFreeCreditAmount();

        if ($this->jackpotTicketExists()) {
            $amount += $this->jackpotTicket->buy_in + $this->jackpotTicket->entry_fee;
        }

        return $amount;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    public function toArray()
    {
        $array =  array(
            "amount" => $this->getAmount(),
            "free_credit_amount" => $this->getFreeCreditAmount(),
            "jackpot_tournament_id" => $this->getJackpotTicket() ? $this->getJackpotTicket()->id : null,
            "position" => $this->position,
        );

        if ($this->getTicket()) {
            $array['user_id'] = $this->getTicket()->user_id;
        }

        return $array;
    }

}