<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/07/2015
 * Time: 3:24 PM
 */

namespace TopBetta\Resources\Accounting;


use TopBetta\Resources\AbstractEloquentResource;

class AccountTransactionResource extends AbstractEloquentResource {

    protected $attributes = array(
        "id" => "id",
        "date" => 'created_date',
        'type' => 'name',
        'description' => 'description',
        'notes' => 'notes',
        'amount' => 'amount'
    );

    private $bet;

    private $ticket;

    private $runningBalance;

    /**
     * @param mixed $runningBalance
     * @return $this
     */
    public function setRunningBalance($runningBalance)
    {
        $this->runningBalance = $runningBalance;
        return $this;
    }

    /**
     * @param mixed $bet
     * @return $this
     */
    public function setBet($bet)
    {
        $this->bet = $bet;
        return $this;
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


    public function toArray()
    {
        $array = parent::toArray();

        if ($this->bet) {
            $array['bet'] = $this->bet->toArray();
        }

        if ($this->ticket) {
            $array['ticket'] = $this->ticket->toArray();
        }

        $array['running_balance'] = $this->runningBalance;

        return $array;
    }

}