<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/04/2015
 * Time: 4:04 PM
 */

namespace TopBetta\Frontend\Controllers;

use App;

class SelectionsController extends AbstractResourceController {

    public function getResourceRepository()
    {
        return App::make('TopBetta\Repositories\Contracts\SelectionRepositoryInterface');
    }
}