<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/07/2015
 * Time: 11:23 AM
 */

namespace TopBetta\Resources\Sports;


use TopBetta\Helpers\TimeHelper;
use TopBetta\Resources\AbstractEloquentResource;

class NextToJumpResource extends AbstractEloquentResource {

    protected static $modelClass = 'TopBetta\Models\EventModel';

    protected $attributes = array(
        "eventId" => "id",
        "competitionId" => "competition_id",
        "baseCompetitionId" => "base_competition_id",
        "sportId" => "sport_id",
        "eventName" => "name",
        "competitionName" => "competition_name",
        "baseCompetitionName" => "base_competition_name",
        "sportName" => "sport_name",
        "start_time" => "start_date",
        "toGo" => "toGo"
    );

    protected $types = array(
        "eventId" => "int",
        "competitionId" => "int",
        "sportId" => "int",
        "baseCompetitionId" => "int",
    );

    public function toGo()
    {
        return TimeHelper::nicetime(strtotime($this->model->start_date), 2);
    }
}