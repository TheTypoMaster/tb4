<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/04/2015
 * Time: 3:36 PM
 */

namespace TopBetta\Http\Controllers\Frontend;

use App;
use TopBetta\Services\Resources\Sports\EventResourceService;

class EventsController extends AbstractResourceController{

    public function getResourceRepository()
    {
        return App::make('TopBetta\Repositories\Contracts\EventRepositoryInterface');
    }

    public function nextToJump(EventResourceService $eventResourceService)
    {
        $nextToJump = $eventResourceService->nextToJump();

        return $this->apiResponse->success($nextToJump->toArray());
    }
}