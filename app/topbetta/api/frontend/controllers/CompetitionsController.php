<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/04/2015
 * Time: 3:16 PM
 */

namespace TopBetta\Frontend\Controllers;

use App;

class CompetitionsController extends AbstractResourceController{

    public function getResourceRepository()
    {
        return App::make('TopBetta\Repositories\Contracts\CompetitionRepositoryInterface');
    }
}