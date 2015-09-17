<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 1/09/2015
 * Time: 9:22 AM
 */

namespace TopBetta\Resources\Tournaments;


class NextToJumpTicketResource extends TicketResource {

    protected function initialize()
    {
        parent::initialize();

        $this->attributes = array_merge($this->attributes, array(
            "eventId" => "event_id",
            "eventName" => "event_name",
            "eventStartDate" => "event_start_date",
            "eventGroupName" => "event_group_name",
            "eventGroupId" => "event_group_id",
        ));
    }
}