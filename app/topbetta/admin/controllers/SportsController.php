<?php namespace TopBetta\admin\controllers;

use Request;
use View;
use Redirect;
use Input;
use TopBetta\Models\IconModel;
use TopBetta\Repositories\Contracts\IconTypeRepositoryInterface;

class SportsController extends CrudResourceController
{
    protected $repositoryName = 'TopBetta\Repositories\Contracts\SportRepositoryInterface';

    protected $iconType = IconTypeRepositoryInterface::TYPE_SPORT;

    protected $modelName = 'Sports';

    protected $indexRoute = 'admin.sports.index';

    protected $editRoute = 'admin.sports.edit';

    protected $createRoute = 'admin.sports.create';

    protected $storeRoute  = 'admin.sports.store';

    protected $updateRoute = 'admin.sports.update';

    protected $deleteRoute = 'admin.sports.destroy';

    protected $indexView = 'admin::eventdata.sports.index';

    protected $createView = 'admin::eventdata.sports.create';

    protected $editView = 'admin::eventdata.sports.edit';

    /**
     * Display a listing of the resource.
     *
     * @param array $extraData
     * @return Response
     */
	public function index($extraData = array())
	{
        $extraData = array(
            "Default Competition Icon" => array(
                "type" => "image",
                "field" => "defaultCompetitionIcon.icon_url"
            ),
        );

        return parent::index($extraData);
	}

    /**
     * Show the form for creating a new resource.
     *
     * @param array $extraData
     * @return Response
     */
	public function create($extraData = array())
	{
        $compIcons = $this->iconService->getIcons(IconTypeRepositoryInterface::TYPE_BASE_COMPETITION);

        $extraData = array(
            "Default Competition Icon" => array(
                "type" => "icon-select",
                "field" => 'default_competition_icon_id',
                'icons' => $compIcons,
            ),
        );

        return parent::create($extraData);
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @param array $extraData
     * @return Response
     */
	public function edit($id, $extraData = array())
	{
		$compIcons = $this->iconService->getIcons(IconTypeRepositoryInterface::TYPE_BASE_COMPETITION);

        $extraData = array(
            "Default Competition Icon" => array(
                "type" => "icon-select",
                "field" => 'default_competition_icon_id',
                'icons' => $compIcons,
            ),
        );

        return parent::edit($id, $extraData);
	}

}
