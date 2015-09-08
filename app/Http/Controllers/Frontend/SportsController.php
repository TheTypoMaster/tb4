<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/04/2015
 * Time: 2:58 PM
 */

namespace TopBetta\Http\Controllers\Frontend;

use App;
use Illuminate\Http\Request;
use TopBetta\Services\Sports\SportsService;


class SportsController extends AbstractResourceController {

    public function getResourceRepository()
    {
        return App::make('TopBetta\Repositories\Contracts\SportRepositoryInterface');
    }

    public function getVisibleSportsWithCompetitions(SportsService $sportService, Request $request)
    {
        $sports = $sportService->getVisibleSportsWithCompetitions($request->get('date', null));

        return $this->apiResponse->success($sports->toArray());
    }

    public function getVisibleSportsWithSelectedCompetition(SportsService $sportService, Request $request)
    {
        $competition = $request->get('competition_id');

        if (!$competition) {
            return $this->apiResponse->failed("No competition specified", 400);
        }

        $sports = $sportService->getVisibleSportsWithCompetitionAndEvent($competition);

        return $this->apiResponse->success($sports['data']->toArray(), 200, array_except($sports, 'data'));
    }
}