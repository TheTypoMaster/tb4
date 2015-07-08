<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/04/2015
 * Time: 4:13 PM
 */

namespace TopBetta\Http\Controllers\Frontend;

use App;

class PlayersController extends AbstractResourceController {

    public function getResourceRepository()
    {
        return App::make('TopBetta\Repositories\Contracts\PlayersRepositoryInterface');
    }

}