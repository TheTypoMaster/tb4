<?php namespace TopBetta\Http\Controllers\Admin;
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/03/2015
 * Time: 10:16 AM
 */

use TopBetta\Repositories\Contracts\CompetitionRegionRepositoryInterface;
use TopBetta\Repositories\Contracts\IconTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;
use TopBetta\Services\Icons\IconService;

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

    protected $indexView = 'admin.eventdata.basecompetitions.index';

    protected $createView = 'admin.eventdata.basecompetitions.create';

    protected $editView = 'admin.eventdata.basecompetitions.edit';

    private $sportRepository;

    private $competitionRegionRepository;

    public function __construct(SportRepositoryInterface $sportRepository, CompetitionRegionRepositoryInterface $competitionRegionRepository, IconService $iconService)
    {
        $this->sportRepository = $sportRepository;
        $this->competitionRegionRepository = $competitionRegionRepository;
        parent::__construct($iconService);
    }


    public function index($relations = array(), $extraData = array())
    {
        $extraData = array(
            "Default Event Group Icon" => array(
                "type" => "image",
                "field" => "defaultEventGroupIcon.icon_url"
            ),
            "Sport" => array(
                "type" => "text",
                "field" => "sport.name",
            ),
            "Region" => array(
                "type" => "text",
                "field" => "region.name",
            ),
        );

        $relations = array(
            'defaultEventGroupIcon',
            'sport',
            'region'
        );

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
        $compIcons = $this->iconService->getIcons(IconTypeRepositoryInterface::TYPE_BASE_COMPETITION);
        $sports = $this->sportRepository->findAll();
        $regions = $this->competitionRegionRepository->findAll();

        return array(
            "Default Event Group Icon" => array(
                "type" => "icon-select",
                "field" => 'default_event_group_icon_id',
                'icons' => $compIcons,
            ),
            "Sport" => array(
                "type" => "select",
                "field" => "sport_id",
                "data" => $sports,
            ),
            "Region" => array(
                "type" => "select",
                "field" => "region_id",
                "data" => $regions,
            ),
        );
    }
}