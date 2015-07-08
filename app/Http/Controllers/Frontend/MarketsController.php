<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/04/2015
 * Time: 3:42 PM
 */

namespace TopBetta\Http\Controllers\Frontend;

use App;

class MarketsController extends AbstractResourceController {

    public function getResourceRepository()
    {
        return App::make('TopBetta\Repositories\Contracts\MarketRepositoryInterface');
    }
}