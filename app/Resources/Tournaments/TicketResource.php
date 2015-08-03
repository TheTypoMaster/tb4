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

    protected $attributes = array(
        'id' => 'id',
        'userId' => 'user_id',
        'tournamentId' => 'tournament_id',
        'rebuys' => 'rebuy_count',
        'topups' => 'topup_count',
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

    public function tournament()
    {
        return $this->item('tournament', 'TopBetta\Resources\Tournaments\TournamentResource', $this->model->tournament);
    }

    public function getAvailableCurrency()
    {
        if( is_null($this->availableCurrency) ) {
            $this->availableCurrency = $this->model->leaderboard->currency - $this->model->bets->where('resulted_flag', false)->sum('bet_amount');
        }

        return $this->availableCurrency;
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
        if( ! $this->model->leaderboard ) dd($this->model);
        return $this->model->leaderboard->turned_over >= $this->model->leaderboard->balance_to_turnover;
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array['qualified'] = $this->getQualified();
        $array['position'] = $this->getPosition() ? : '-';
        $array['available_currency'] = $this->getAvailableCurrency();

        return $array;
    }

}