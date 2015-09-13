<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/04/2015
 * Time: 3:46 PM
 */

namespace TopBetta\Http\Controllers\Frontend;

use App;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use TopBetta\Services\Sports\MarketService;

class MarketTypesController extends AbstractResourceController {

    public function getResourceRepository()
    {
        return App::make('TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface');
    }

    public function getMarketTypesForCompetition(MarketService $marketService, Request $request)
    {
        try {
            $marketTypes = $marketService->getMarketTypesForCompetition($request->get('competition_id'));
        } catch (ModelNotFoundException $e) {
            return $this->apiResponse->failed("Competition not found", 404);
        }

        return $this->apiResponse->success($marketTypes['data']->toArray(), 200, array("selected" => $marketTypes['selected_types']));
    }
}