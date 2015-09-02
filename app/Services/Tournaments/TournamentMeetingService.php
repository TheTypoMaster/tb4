<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 31/08/2015
 * Time: 4:01 PM
 */

namespace TopBetta\Services\Tournaments;


use TopBetta\Services\Racing\MeetingService;
use App;

class TournamentMeetingService extends MeetingService {

    public function setMeetingResourceService()
    {
        $this->meetingResourceService = App::make('TopBetta\Services\Resources\Tournaments\TournamentMeetingResourceService');
        return $this;
    }
}