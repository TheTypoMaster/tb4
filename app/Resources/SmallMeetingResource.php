<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 8/09/2015
 * Time: 3:12 PM
 */

namespace TopBetta\Resources;


class SmallMeetingResource extends MeetingResource {

    protected $attributes = array(
        "id"             => 'id',
        "name"           => 'name',
        "state"          => 'state',
        "track"          => 'track',
        "weather"        => 'weather',
        "type"           => 'type_code',
        'startDate'      => 'start_date',
        "grade"          => 'meeting_grade',
        "country"       => "country",
        "nextRaceDate"   => "nextRaceDate",
        "nextRaceNumber" => "nextRaceNumber",
        "ordering"      => "ordering",
    );

    /**
     * @return EloquentResourceCollection
     */
    public function races()
    {
        return $this->collection('races', 'TopBetta\Resources\SmallRaceResource', 'competitionEvents');
    }
}