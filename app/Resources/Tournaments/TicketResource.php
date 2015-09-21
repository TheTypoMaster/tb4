<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/07/2015
 * Time: 2:25 PM
 */

namespace TopBetta\Resources\Tournaments;

use TopBetta\Resources\AbstractEloquentResource;

class TicketResource extends AbstractEloquentResource {

    protected static $modelClass = 'TopBetta\Models\TournamentTicketModel';

    protected $attributes = array(
        'id' => 'id',
        'userId' => 'user_id',
        'tournamentId' => 'tournament_id',
        'rebuys' => 'rebuy_count',
        'topups' => 'topup_count',
        'turnedOver' => 'turnedOver',
        'balanceToTurnover' => 'balanceToTurnover',
    );

    protected $types = array(
        'id' => 'int',
        'userId' => 'int',
        'tournamentId' => 'int'
    );

    protected $loadIfRelationExists = array(
        'tournament' => 'tournament'
    );

    private $availableCurrency = null;

    private $position = null;

    public static function createResourceFromArray($array, $resource = null)
    {
        $resource = parent::createResourceFromArray($array, $resource);

        $resource->setAvailableCurrency($array['available_currency']);
        $resource->setPosition($array['position']);

        return $resource;
    }

    public function tournament()
    {
        return $this->item('tournament', 'TopBetta\Resources\Tournaments\TournamentResource', $this->model->tournament);
    }

    public function getAvailableCurrency()
    {
        if( is_null($this->availableCurrency) && $this->model->leaderboard ) {
            $this->availableCurrency = $this->model->leaderboard->currency - $this->model->bets->whereLoose('resulted_flag', false)->sum('bet_amount');
        }

        return $this->availableCurrency;
    }

    /**
     * @param null $availableCurrency
     * @return $this
     */
    public function setAvailableCurrency($availableCurrency)
    {
        $this->availableCurrency = $availableCurrency;
        return $this;
    }

    public function addAvailableCurrency($currency)
    {
        $this->availableCurrency = $this->getAvailableCurrency() + $currency;
        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getQualified()
    {
        if( ! $this->getBalanceToTurnover() ) {
            return false;
        }

        return $this->getBalanceToTurnover() <= $this->getTurnedOver();
    }

    public function getTurnedOver()
    {
        if ($this->model->turned_over) {
            return $this->model->turned_over;
        }

        if ($this->model->leaderboard) {
            return $this->model->leaderboard->turned_over;
        }

        return 0;
    }

    public function getBalanceToTurnover()
    {
        if ($this->model->balance_to_turnover) {
            return $this->model->balance_to_turnover;
        }

        if ($this->model->leaderboard) {
            return $this->model->leaderboard->balance_to_turnover;
        }

        return 0;
    }

    public function setTurnedOver($turnover)
    {
        $this->model->turned_over = $turnover;
        return $this;
    }

    public function setBalanceToTurnover($balance)
    {
        $this->model->balance_to_turnover = $balance;
        return $this;
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array['qualified'] = $this->getQualified();
        $array['position'] = $this->getPosition() ? : '-';
        $array['available_currency'] = $this->getAvailableCurrency();

        return $array;
    }

    public function initialize()
    {
        parent::initialize();

        if( $this->getQualified() ) {
            $leaderboardService = \App::make('TopBetta\Services\Tournaments\TournamentLeaderboardService');
            $this->setPosition(
                $leaderboardService->getLeaderboardPositionForTicket($this->model)
            );
        }

    }

}