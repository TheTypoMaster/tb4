<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/07/2015
 * Time: 3:56 PM
 */

namespace TopBetta\Resources\Tournaments;


use TopBetta\Resources\AbstractEloquentResource;

class TournamentResource extends AbstractEloquentResource {

    protected $attributes = array(
        'id'                    => 'id',
        'name'                  => 'name',
        'description'           => 'description',
        'currency'              => 'start_currency',
        'startDate'             => 'start_date',
        'endDate'               => 'end_date',
        'jackpot'               => 'jackpot_flag',
        'buyin'                 => 'buy_in',
        'entryFee'              => 'entry_fee',
        'minimumPrizePool'      => 'minimum_prize_pool',
        'paid'                  => 'paid_flag',
        'bettingClosed'         => 'betting_closed_date',
        'betLimitPerEvent'      => 'bet_limit_per_event',
        'rebuys'                => 'rebuys',
        'rebuyEntryFee'         => 'rebuy_entry',
        'rebuyBuyin'            => 'rebuy_buyin',
        'rebuyCurrency'         => 'rebuy_currency',
        'rebuyEnd'              => 'rebuy_end',
        'topups'                => 'topups',
        'topupEntryFee'         => 'topup_entry',
        'topupBuyin'            => 'topup_buyin',
        'topupCurrency'         => 'topup_currency',
        'topupStart'            => 'topup_start',
        'topup_end'             => 'topup_end',
        'tournamentSponsor'     => 'tournament_sponsor_name',
        'tournamentSponsorLogo' => 'tournament_sponsor_logo',
    );

    protected $types = array(
        "id"               => "int",
        "currency"         => "int",
        "jackpot"          => "bool",
        "buyin"            => "int",
        "entryFee"         => "int",
        "minimumPrizePool" => "int",
        "paid"             => "bool",
        "betLimitPerEvent" => "int",
        "rebuys"           => "int",
        'rebuyEntryFee'    => 'int',
        'rebuyBuyin'       => 'int',
        'rebuyCurrency'    => 'int',
        'topups'           => 'int',
        'topupEntryFee'    => 'int',
        'topupBuyin'       => 'int',
        'topupCurrency'    => 'int',
    );

    private $entrants = null;

    private $prizePool = null;

    private $leaderboard = array();

    private $meetings = array();

    private $competitions = array();

    public function getEntrants()
    {
        if( is_null($this->entrants) ) {
            $this->entrants = $this->model->tickets->count();
        }

        return $this->entrants;
    }

    public function getPrizePool()
    {
        if( is_null($this->prizePool) ) {
            $this->prizePool = max($this->minimumPrizePool, $this->buyin * $this->getEntrants());
        }

        return $this->prizePool;
    }

    public function setLeaderboard($leaderboard)
    {
        $this->leaderboard = $leaderboard;
        return $this;
    }

    public function addMeeting($meeting)
    {
        $this->meetings[] = $meeting;
        return $this;
    }

    public function addCompetition($competition)
    {
        $this->competitions[] = $competition;
        return $this;
    }


    public function toArray()
    {
        $array = parent::toArray();

        $array = array_merge($array, array(
            'entrants' => $this->getEntrants(),
            'prize_pool' => $this->getPrizePool(),
        ));

        if ($this->leaderboard) {
            $array['leaderboard'] = $this->leaderboard;
        }

        if (count($this->meetings)) {
            $array['meetings'] = array_map(function($v) { return $v->toArray(); }, $this->meetings);
        }

        if (count($this->competitions)) {
            $array['competitions'] = array_map(function($v) { return $v->toArray(); }, $this->competitions);
        }

        return $array;
    }

}