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

        $array['bet_id'] = $this->bet ? $this->bet->id : null;
        $array['ticket_id'] = $this->ticket ? $this->ticket->id : null;

        return $array;
    }

}