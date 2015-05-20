<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/03/2015
 * Time: 1:04 PM
 */

namespace TopBetta\admin\controllers;


use TopBetta\Repositories\Contracts\IconTypeRepositoryInterface;

class TeamController extends CrudResourceController {

    protected $repositoryName = 'TopBetta\Repositories\Contracts\TeamRepositoryInterface';

    protected $iconType = IconTypeRepositoryInterface::TYPE_TEAM;

    protected $modelName = 'Teams';

    protected $indexRoute = 'admin.teams.index';

    protected $editRoute = 'admin.teams.edit';

    protected $createRoute = 'admin.teams.create';

    protected $storeRoute  = 'admin.teams.store';

    protected $updateRoute = 'admin.teams.update';

    protected $deleteRoute = 'admin.teams.destroy';

    protected $indexView = 'admin::eventdata.teams.index';

    protected $createView = 'admin::eventdata.teams.create';

    protected $editView = 'admin::eventdata.teams.edit';


}