<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/03/2015
 * Time: 10:16 AM
 */

namespace TopBetta\admin\controllers;

use TopBetta\Repositories\Contracts\IconTypeRepositoryInterface;

class BaseCompetitionController extends CrudResourceController
{

    protected $repositoryName = 'TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface';

    protected $iconType = IconTypeRepositoryInterface::TYPE_BASE_COMPETITION;

    protected $modelName = 'Base Competitions';

    protected $indexRoute = 'admin.basecompetitions.index';

    protected $editRoute = 'admin.basecompetitions.edit';

    protected $createRoute = 'admin.basecompetitions.create';

    protected $storeRoute  = 'admin.basecompetitions.store';

    protected $updateRoute = 'admin.basecompetitions.update';

    protected $deleteRoute = 'admin.basecompetitions.destroy';

    protected $indexView = 'admin::eventdata.basecompetitions.index';

    protected $createView = 'admin::eventdata.basecompetitions.create';

    protected $editView = 'admin::eventdata.basecompetitions.edit';


    public function index($relations = array(), $extraData = array())
    {
        $extraData = array(
            "Default Event Group Icon" => array(
                "type" => "image",
                "field" => "defaultEventGroupIcon.icon_url"
            ),
        );

        $relations = array(
            'defaultEventGroupIcon'
        );

        return parent::index($relations, $extraData);
    }

    public function create($extraData = array())
    {
        $compIcons = $this->iconService->getIcons(IconTypeRepositoryInterface::TYPE_BASE_COMPETITION);

        $extraData = array(
            "Default Event Group Icon" => array(
                "type" => "icon-select",
                "field" => 'default_event_group_icon_id',
                'icons' => $compIcons,
            ),
        );

        return parent::create($extraData);
    }

    public function edit($id, $extraData = array())
    {
        $compIcons = $this->iconService->getIcons(IconTypeRepositoryInterface::TYPE_BASE_COMPETITION);

        $extraData = array(
            "Default Event Group Icon" => array(
                "type" => "icon-select",
                "field" => 'default_event_group_icon_id',
                'icons' => $compIcons,
            ),
        );

        return parent::edit($id, $extraData);
    }
}