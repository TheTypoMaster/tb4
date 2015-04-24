<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/03/2015
 * Time: 1:04 PM
 */

namespace TopBetta\admin\controllers;


use TopBetta\Repositories\Contracts\IconTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\TeamRepositoryInterface;
use TopBetta\Services\Icons\IconService;

class PlayerController extends CrudResourceController {

    protected $repositoryName = 'TopBetta\Repositories\Contracts\PlayersRepositoryInterface';

    protected $iconType = IconTypeRepositoryInterface::TYPE_PLAYER;

    protected $modelName = 'Players';

    protected $indexRoute = 'admin.players.index';

    protected $editRoute = 'admin.players.edit';

    protected $createRoute = 'admin.players.create';

    protected $storeRoute = 'admin.players.store';

    protected $updateRoute = 'admin.players.update';

    protected $deleteRoute = 'admin.players.destroy';

    protected $indexView = 'admin::eventdata.players.index';

    protected $createView = 'admin::eventdata.players.create';

    protected $editView = 'admin::eventdata.players.edit';

    protected $excludedFields = array('description');

    private $teamRepository;

    public function __construct(TeamRepositoryInterface $teamRepository, IconService $iconService)
    {
        $this->teamRepository = $teamRepository;
        parent::__construct($iconService);
    }

    public function index($relations = array(), $extraData = array())
    {
        $extraData = array(
            "Teams" => array(
                "type" => "closure",
                "field" => function($model) {
                    return implode(', ', $model->teams->lists('name'));
                }
            )
        );

        $relations = array('teams');

        return parent::index($relations, $extraData);
    }

    public function create($extraData = array())
    {
        return parent::create($this->getExtraData());
    }

    public function edit($id, $extraData = array())
    {
        return parent::edit($id, $this->getExtraData());
    }

    private function getExtraData()
    {
        $data = $this->teamRepository->findAll();

        return array(
            "Teams" => array(
                'field' => 'teams',
                "type" => "multi-select",
                "multiple" => true,
                "data" => $data
            )
        );
    }


}