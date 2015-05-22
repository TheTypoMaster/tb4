<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/03/2015
 * Time: 3:52 PM
 */

namespace TopBetta\admin\controllers;

use TopBetta\Repositories\Contracts\IconTypeRepositoryInterface;

class CompetitionRegionController extends  CrudResourceController {

    protected $repositoryName = 'TopBetta\Repositories\Contracts\CompetitionRegionRepositoryInterface';

    protected $iconType = IconTypeRepositoryInterface::TYPE_REGION;

    protected $modelName = 'Regions';

    protected $indexRoute = 'admin.competitionregions.index';

    protected $editRoute = 'admin.competitionregions.edit';

    protected $createRoute = 'admin.competitionregions.create';

    protected $storeRoute  = 'admin.competitionregions.store';

    protected $updateRoute = 'admin.competitionregions.update';

    protected $deleteRoute = 'admin.competitionregions.destroy';

    protected $indexView = 'admin::eventdata.regions.index';

    protected $createView = 'admin::eventdata.regions.create';

    protected $editView = 'admin::eventdata.regions.edit';
    
}