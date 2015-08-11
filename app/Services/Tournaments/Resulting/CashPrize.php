<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/08/2015
 * Time: 3:33 PM
 */

namespace TopBetta\Services\Tournaments\Resulting;


use TopBetta\Services\Accounting\AccountTransactionService;

class CashPrize implements TournamentPrize {

    /**
     * @var
     */
    private $ticket;

    /**
     * @var
     */
    private $amount;

    /**
     * @var
     */
    private $accountTransactionService;

    public function __construct(AccountTransactionservice $accountTransactionService)
    {
        $this->accountTransactionService = $accountTransactionService;
    }

    public function payout()
    {

    }

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


}