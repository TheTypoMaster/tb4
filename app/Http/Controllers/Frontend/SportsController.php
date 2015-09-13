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
use TopBetta\Services\Resources\Cache\Sports\CachedSportResourceService;
use TopBetta\Services\Sports\SportsService;


class SportsController extends AbstractResourceController {

    public function getResourceRepository()
    {
        return App::make('TopBetta\Repositories\Contracts\SportRepositoryInterface');
    }

    public function getVisibleSportsWithCompetitions(SportsService $sportService, Request $request)
    {
        $sport = $request->get('sport_id');

        if(!$sport) {
            return $this->apiResponse->failed("No sport specified", 400);
        }

        $sports = $sportService->getSportsWithCompetitionsForSport($sport);

        return $this->apiResponse->success($sports->toArray());
    }

    public function getVisibleSportsWithSelectedCompetition(SportsService $sportService, Request $request)
    {
        $competition = $request->get('competition_id');

        if (!$competition) {
            return $this->apiResponse->failed("No competition specified", 400);
        }

        $sports = $sportService->getSportsWithCompetitionsAndEventForCompetition($competition);

        return $this->apiResponse->success($sports->toArray(), 200);
    }

    public function getVisibleSports(CachedSportResourceService $sportsService)
    {
        $sports = $sportsService->getVisibleSports();

        return $this->apiResponse->success($sports->toArray());
    }


}