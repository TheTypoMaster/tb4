<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 8/07/2015
 * Time: 1:15 PM
 */

namespace TopBetta\Http\Controllers\Frontend;

use App;

class ResultsController extends AbstractResourceController {

    public function getResourceRepository()
    {
        return App::make('TopBetta\Repositories\Contracts\SelectionResultRepositoryInterface');
    }
}