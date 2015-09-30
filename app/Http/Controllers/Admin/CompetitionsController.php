<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;

use Request;
use TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\IconTypeRepositoryInterface;
use TopBetta\Services\Icons\IconService;
use View;
use BaseController;
use Redirect;
use Input;

use TopBetta\Repositories\DbCompetitionRepository;
use TopBetta\Repositories\DbSportsRepository;
use TopBetta\Services\DataManagement\CompetitionService;

class CompetitionsController extends CrudResourceController
{

    protected $repositoryName = 'TopBetta\Repositories\Contracts\CompetitionRepositoryInterface';

    protected $iconType = IconTypeRepositoryInterface::TYPE_EVENT_GROUP;

    protected $modelName = 'Competitions';

    protected $indexRoute = 'admin.competitions.index';

    protected $editRoute = 'admin.competitions.edit';

    protected $createRoute = 'admin.competitions.create';

    protected $storeRoute  = 'admin.competitions.store';

    protected $updateRoute = 'admin.competitions.update';

    protected $deleteRoute = 'admin.competitions.destroy';

    protected $indexView = 'admin.eventdata.competitions.index';

    protected $createView = 'admin.eventdata.competitions.create';

    protected $editView = 'admin.eventdata.competitions.edit';

    protected $createChildRoute = array(
        "route" => 'admin.events.create',
        'param' => 'competition_id',
        'name' => 'Event'
    );

    private $baseCompetitionRepository;


    public function __construct(BaseCompetitionRepositoryInterface $baseCompetitionRepository, IconService $iconService)
    {
        $this->baseCompetitionRepository = $baseCompetitionRepository;
        parent::__construct($iconService);
    }
    /**
     * Display a listing of the resource.
     *
     * @param array $relations
     * @param array $extraData
     * @return Response
     */
	public function index($relations = array(), $extraData = array())
	{

		$extraData = array(
            "Base Competition" => array(
                'field' => 'baseCompetition.name',
                'type' => 'text'
            ),
            "Default Event Icon" => array(
                'field' => 'defaultEventIcon.icon_url',
                'type'  => 'image'
            ),
            "Start Date" => array(
                "field" => 'start_date',
                'type'  => 'datetime'
            ),
            "Close Date" => array(
                "field" => 'close_time',
                'type'  => 'datetime'
            ),
        );

        $relations = array(
            'defaultEventIcon',
            'baseCompetition'
        );

        return parent::index($relations, $extraData);
	}

    /**
     * Show the form for creating a new resource.
     *
     * @param array $extraData
     * @return Response
     */
	public function create($extraData = array())
	{
        $extraData = $this->getExtraData();

        return parent::create($extraData);

	}

    public function edit($id, $extraData = array())
    {
        $extraData = $this->getExtraData();

        return parent::edit($id, $extraData);
    }

    private function getExtraData()
    {
        $eventIcons = $this->iconService->getIcons(IconTypeRepositoryInterface::TYPE_BASE_COMPETITION);

        return array(
            "Base Competition" => array(
                'field' => 'base_competition_id',
                'type' => 'select',
                'data'  => $this->baseCompetitionRepository->findAll()
            ),
            "Default Event Icon" => array(
                'field' => 'default_event_icon_id',
                'type'  => 'icon-select',
                'icons' => $eventIcons
            ),
            "Start Date" => array(
                "field" => 'start_date',
                'type'  => 'datetime'
            ),
            "Close Date" => array(
                "field" => 'close_time',
                'type'  => 'datetime'
            ),
        );
    }

    public function update($id, $extraData = array())
    {
        $baseCompetition = \App::make('TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface')->find(Input::get('base_competition_id'));

        if( ! $baseCompetition ) {
            return Redirect::back()
                ->withInput()->with(array('flash_message' => 'please specify base competition'));
        }

        $data = array('sport_id' => $baseCompetition->sport_id);

        return parent::update($id, $data);
    }

    public function store($extraData = array())
    {
        $baseCompetition = \App::make('TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface')->find(Input::get('base_competition_id'));

        if( ! $baseCompetition ) {
            return Redirect::back()
                ->withInput()->with(array('flash_message' => 'please specify base competition'));
        }

        $data = array('sport_id' => $baseCompetition->sport_id);

        return parent::store($data);
    }

    public function getBySport($id)
    {
        return $this->repository->findBySport($id)->toArray();
    }

}
