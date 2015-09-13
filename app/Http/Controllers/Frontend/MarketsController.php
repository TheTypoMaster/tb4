<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/04/2015
 * Time: 3:42 PM
 */

namespace TopBetta\Http\Controllers\Frontend;

use App;
use Illuminate\Http\Request;
use TopBetta\Services\Sports\MarketService;

class MarketsController extends AbstractResourceController {

    public function getResourceRepository()
    {
        return App::make('TopBetta\Repositories\Contracts\MarketRepositoryInterface');
    }

    public function getAllMarketsForEvent(Request $request, MarketService $marketService)
    {
        $markets = $marketService->getAllMarketsForEvent($request->get('event_id', 0));

        return $this->apiResponse->success($markets->toArray());
    }
}