<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/04/2015
 * Time: 3:36 PM
 */

namespace TopBetta\Http\Controllers\Frontend;

use App;
use Illuminate\Http\Request;
use TopBetta\Services\Resources\Cache\Sports\CachedEventResourceService;
use TopBetta\Services\Resources\Sports\EventResourceService;
use TopBetta\Services\Sports\CompetitionService;
use TopBetta\Services\Sports\EventService;

class EventsController extends AbstractResourceController{

    public function getResourceRepository()
    {
        return App::make('TopBetta\Repositories\Contracts\EventRepositoryInterface');
    }

    public function nextToJump(CachedEventResourceService $eventResourceService)
    {
        $nextToJump = $eventResourceService->nextToJump();

        return $this->apiResponse->success($nextToJump->toArray());
    }

    public function getEventsForCompetition(CompetitionService $competitionService, Request $request)
    {
        $events = $competitionService->getCompetitionsWithEvents($request->only(array('competition_id', 'base_competition_id')), $request->get('types', null));

        return $this->apiResponse->success($events['data']->toArray(), 200, array('selected_competition' => $events['selected_competition']));
    }
}