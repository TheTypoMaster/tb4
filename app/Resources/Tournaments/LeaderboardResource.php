<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/07/2015
 * Time: 12:21 PM
 */

namespace TopBetta\Resources\Tournaments;


use TopBetta\Resources\AbstractEloquentResource;

class LeaderboardResource extends AbstractEloquentResource {

    protected static $modelClass = 'TopBetta\Models\TournamentLeaderboardModel';

    protected $attributes = array(
        "id" => "id",
        "userId" => "user_id",
        "username" => "username",
        "currency" => 'currency',
        'turned_over' => 'turned_over',
        'rebuys' => 'rebuys',
        'topups' => 'topups',
        'qualified' => 'qualified',
    );

    protected $types = array(
        "id" => "int",
        "currency" => "int",
        "turned_over" => "int",
        "rebuys" => "int",
        "topups" => "int",
        "qualified" => "bool",
        "userId" => "int",
    );

    private $position = '-';

    public static function createResourceFromArray($array, $resource = null)
    {
        $resource = parent::createResourceFromArray($array);

        if ($position = $resource->getModel()->position) {
            $resource->setPosition($position);
        }

        return $resource;
    }

    public function qualified()
    {
        if (isset($this->model->qualified)) {
            return $this->model->qualified;
        }

        return $this->model->turned_over >= $this->model->balance_to_turnover && $this->currency > 0;
    }

    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    public function user_id()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    public function getUsername()
    {
        if ($this->model->username) {
            return $this->model->username;
        }

        return $this->model->user->username;
    }

    public function rebuys()
    {
        if (isset($this->model->rebuys)) {
            return $this->model->rebuys;
        }

        if ($this->model->ticket) {
            return $this->model->ticket->rebuy_count;
        }

        return 0;
    }

    public function topups()
    {
        if (isset($this->model->topups)) {
            return $this->model->topups;
        }

        if ($this->model->ticket) {
            return $this->model->ticket->topup_count;
        }

        return 0;
    }

    /**
     * Compare currency and qualifed with leaderboard record
     * @param LeaderboardResource $leaderboardResource
     * @return int
     */
    public function compare(LeaderboardResource $leaderboardResource)
    {
        if (!$this->qualified() && !$leaderboardResource->qualified()) {
            return 0;
        }

        if ($this->qualified() && !$leaderboardResource->qualified()) {
            return 1;
        }

        if (!$this->qualified() && $leaderboardResource->qualified()) {
            return -1;
        }

        if ($this->currency == $leaderboardResource->currency) {
            return 0;
        }

        return $this->currency > $leaderboardResource->currency ? 1 : -1;
    }

    public function intialize()
    {
        parent::initialize();

        if (!$this->model->username) {
            $this->model->username = $this->model->user->username;
        }
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array['position'] = $this->getPosition();

        return $array;
    }

}