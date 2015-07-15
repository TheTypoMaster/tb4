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
}