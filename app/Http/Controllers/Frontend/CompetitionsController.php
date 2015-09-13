<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/04/2015
 * Time: 3:16 PM
 */

namespace TopBetta\Http\Controllers\Frontend;

use App;
use Illuminate\Http\Request;
use TopBetta\Services\Resources\Cache\Sports\CachedBaseCompetitionResourceService;

class CompetitionsController extends AbstractResourceController{

    public function getResourceRepository()
    {
        return App::make('TopBetta\Repositories\Contracts\CompetitionRepositoryInterface');
    }

    public function getCompetitionsForSport(CachedBaseCompetitionResourceService $baseCompetitionResourceService, Request $request)
    {
        $sport = $request->get('sport_id');

        if(!$sport) {
            return $this->apiResponse->failed("No sport specified", 400);
        }

        $competitions = $baseCompetitionResourceService->getBaseCompetitionsForSportWithCompetitions($sport);

        return $this->apiResponse->success($competitions->toArray());
    }
}