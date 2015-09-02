<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/07/2015
 * Time: 11:06 AM
 */

namespace TopBetta\Resources;


use Carbon\Carbon;

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
        "railPosition"  => 'rail_position'
    );

    protected $loadIfRelationExists = array(
        'competitionEvents' => 'races'
    );

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

    /**
     * @return Carbon
     */
    public function getStartDate()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->model->start_date);
    }
}