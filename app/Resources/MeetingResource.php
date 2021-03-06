<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/07/2015
 * Time: 11:06 AM
 */

namespace TopBetta\Resources;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;

class MeetingResource extends AbstractEloquentResource {
    
    protected $attributes = array(
        "id"            => 'id',
        "name"          => 'name',
        "description"   => 'description',
        "state"         => 'state',
        "track"         => 'track',
        "weather"       => 'weather',
        "type"          => 'type_code',
        "start_date"    => 'start_date',
        "country"       => 'country',
        "grade"         => 'meeting_grade',
        "railPosition"  => 'rail_position',
        "nextRaceDate"  => "nextRaceDate",
        "nextRaceNumber" => "nextRaceNumber",
        "ordering" => "ordering",
        "fixed_odds_enabled" => "fixed_odds_enabled",
    );


    protected $loadIfRelationExists = array(
        'competitionEvents' => 'races',
        'races'             => 'races',
    );

    protected static $modelClass = 'TopBetta\Models\CompetitionModel';

    /**
     * @return EloquentResourceCollection
     */
    public function races()
    {
        return $this->collection('races', 'TopBetta\Resources\RaceResource', 'competitionEvents');
    }

    public function setRaces($races)
    {
        $this->relations['races'] = $races;
    }

    public function setOrdering($ordering)
    {
        $this->model->ordering = $ordering;
        return $this;
    }

    public function getOrdering()
    {
        return $this->model->ordering ? : 0;
    }

    /**
     * @return Carbon
     */
    public function getStartDate()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->model->start_date)->toDateTimeString();
    }

    public function setNextRaceDate($date)
    {
        $this->model->nextRaceDate = $date;
        return $this;
    }

    public function getNextRaceDate()
    {
        return $this->model->nextRaceDate;
    }

    public function setNextRaceNumber($number)
    {
        $this->model->nextRaceNumber = $number;
        return $this;
    }

    public function getNextRaceNumber()
    {
        return $this->model->nextRaceNumber;
    }

    public function setRelation($name, $collection)
    {
        parent::setRelation($name, $collection);

        if ($name == 'races') {
            foreach ($this->relations['races'] as $race) {
                if ($race->status == EventStatusRepositoryInterface::STATUS_SELLING) {
                    $this->setNextRaceDate($race->start_date);
                    $this->setNextRaceNumber($race->number);
                    break;
                }
            }
        }
    }

    public function loadRelation($name)
    {
        parent::loadRelation($name);

        if ($name == 'races') {
            foreach ($this->relations['races'] as $race) {
                if ($race->status == EventStatusRepositoryInterface::STATUS_SELLING) {
                    $this->setNextRaceDate($race->start_date);
                    $this->setNextRaceNumber($race->number);
                    break;
                }
            }
        }
    }


    public function toArray()
    {
        $array = parent::toArray();

        if (!$this->getNextRaceDate() && $races = array_get($this->relations, 'races')) {
            foreach ($this->relations['races'] as $race) {
                if ($race->status == EventStatusRepositoryInterface::STATUS_SELLING) {
                    $array['next_race_date'] = $race->start_date;
                    $array['next_race_number'] = $race->number;
                    break;
                }
            }
        }

        return $array;
    }

    public function without($relation)
    {
        if (isset($this->relations[$relation])) {
            unset($this->relations[$relation]);
        }
    }

    public function initialize()
    {
        parent::initialize();

        //set custom ordering for the meeting
        $repository = \App::make('TopBetta\Repositories\DbMeetingVenueRepository');
        $venue = $repository->getByName($this->model->name);

        if ($venue) {
            $this->setOrdering($venue->ordering);
        }
    }
}