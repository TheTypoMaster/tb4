<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 31/08/2015
 * Time: 3:08 PM
 */

namespace TopBetta\Services\Resources\Tournaments;


use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\MeetingResource;
use TopBetta\Services\Resources\MeetingResourceService;

class TournamentMeetingResourceService extends MeetingResourceService {

    public function loadTotesForMeeting(MeetingResource $meetingResource)
    {
        $products = new EloquentResourceCollection($meetingResource->getModel()->products, 'TopBetta\Resources\ProductResource');

        foreach ($meetingResource->races as $race) {
            $race->setProducts($products);
        }

        return $meetingResource;
    }
}