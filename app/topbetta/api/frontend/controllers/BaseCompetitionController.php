<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/04/2015
 * Time: 3:30 PM
 */

namespace TopBetta\Frontend\Controllers;

use App;

class BaseCompetitionController extends AbstractResourceController {

    public function getResourceRepository()
    {
        return App::make('TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface');
    }
}