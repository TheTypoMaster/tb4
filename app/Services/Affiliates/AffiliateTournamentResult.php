<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 13/08/2015
 * Time: 12:30 PM
 */

namespace TopBetta\Services\Affiliates;


use TopBetta\Services\Tournaments\Resulting\TournamentResult;

class AffiliateTournamentResult {

    /**
     * @var TournamentResult
     */
    private $result;

    public function __construct(TournamentResult $result)
    {
        $this->result = $result;
    }

    public function toArray()
    {
        return array(
            "tournament_username" => $this->result->getTicket()->user->username,
            "external_unique_identifier" => $this->result->getTicket()->user->external_user_id,
            "finishing_position" => $this->result->getPosition(),
            "return_amount" => $this->result->getAmount(),
        );
    }
}