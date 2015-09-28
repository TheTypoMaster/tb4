<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/07/2015
 * Time: 3:56 PM
 */

namespace TopBetta\Resources\Tournaments;


use TopBetta\Repositories\TournamentEventGroupRepository;
use TopBetta\Resources\AbstractEloquentResource;

class TournamentResource extends AbstractEloquentResource {

    protected static $modelClass = 'TopBetta\Models\TournamentModel';

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
        'tournamentPrizeFormat' => 'tournament_prize_format',
        'tournamentPrizeShortName' => 'prizeFormat.short_name',
        'tournamentPrizeIcon' => 'prizeFormat.icon',
        'tournamentType'       => 'tournament_type',
        'tournamentMixed'       => 'tournament_mixed',
        'type'                  => 'type',
        'entrants'              => 'entrants',
        'event_group_id'        => 'event_group_id'
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
        'event_group_id'   => 'int',
        'tournamentMixed' => 'bool',
    );

    private $entrants = null;

    private $prizePool = null;

    private $leaderboard = array();

    private $meetings = array();

    private $competitions = array();

    private $results = null;

    private $type;

    private $prizeFormat;


    public function getEntrants()
    {
        if (isset($this->model->entrants)) {
            return $this->model->entrants;
        }

        if( is_null($this->entrants) ) {
            $this->entrants = $this->model->tickets->count();
        }

        return $this->entrants;
    }

    public function addEntrant()
    {
        if (isset($this->model->entrants)) {
            $this->model->entrants++;
            return;
        }


        $this->entrants = $this->getEntrants() + 1;
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
        $this->setRelation('leaderboard', $leaderboard);
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

    public function setResults($results)
    {
        $this->results = $results;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        if (!$this->type) {
            return $this->model->type;
        }
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getTournamentPrizeFormat()
    {
        if (object_get($this->model, 'tournament_prize_format') && !is_numeric($this->model->tournament_prize_format)) {
            return $this->model->tournament_prize_format;
        }

        if ($this->prizeFormat) {
            return $this->prizeFormat;
        }

        return $this->model->prizeFormat;
    }

    /**
     * @param mixed $prizeFormat
     * @return $this
     */
    public function setPrizeFormat($prizeFormat)
    {
        $this->prizeFormat = $prizeFormat;
        return $this;
    }

    /**
     * @param mixed $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getResults()
    {
        if(isset($this->model->prizes)) {
            return $this->model->prizes;
        }

        return $this->results;
    }


    public function initialize()
    {
        parent::initialize();

        $tournamentEventGroupRepository = \App::make('TopBetta\Repositories\TournamentEventGroupRepository');
        $event_group = $tournamentEventGroupRepository->getEventGroup($this->model->event_group_id);
        $this->setType($event_group->type);

//        $this->setType($this->model->eventGroup->sport_id > 3 ? 'sport' : 'racing');


        $this->setPrizeFormat($this->model->prizeFormat->name);
        
        $resultService = \App::make('TopBetta\Services\Tournaments\TournamentResultService');
        $this->setResults($resultService->getTournamentResults($this->model)->values());
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array = array_merge($array, array(
            'entrants' => $this->getEntrants(),
            'prize_pool' => $this->getPrizePool(),
            'prizes' => is_array($this->getResults()) ? $this->getResults() : $this->getResults()->toArray()
        ));

        if ($this->leaderboard) {
            $array['leaderboard'] = $this->leaderboard;
        }

        if (count($this->meetings)) {
            $array['meetings'] = $this->meetings;
        }

        if (count($this->competitions)) {
            $array['competitions'] = $this->competitions;
        }

        return $array;
    }

    /**
     * @param array $meetings
     * @return $this
     */
    public function setMeetings($meetings)
    {
        $this->meetings = $meetings;
        return $this;
    }

    /**
     * @param array $competitions
     * @return $this
     */
    public function setCompetitions($competitions)
    {
        $this->competitions = $competitions;
        return $this;
    }




}