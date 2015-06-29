<?php namespace TopBetta\Http\Controllers\Admin;

use Input;
use Redirect;
use View;

use TopBetta\Http\Controllers\Controller;

use TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface;
use TopBetta\Services\Markets\MarketOrderingService;

class MarketOrderingController extends Controller {

    /**
     * @var MarketOrderingService
     */
    private $marketOrderingService;
    /**
     * @var MarketTypeRepositoryInterface
     */
    private $marketTypeRepository;
    /**
     * @var BaseCompetitionRepositoryInterface
     */
    private $baseCompetitionRepository;

    public function __construct(MarketOrderingService $marketOrderingService,
                                MarketTypeRepositoryInterface $marketTypeRepository,
                                BaseCompetitionRepositoryInterface $baseCompetitionRepository)
    {
        $this->marketOrderingService = $marketOrderingService;
        $this->marketTypeRepository = $marketTypeRepository;
        $this->baseCompetitionRepository = $baseCompetitionRepository;
    }

    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

        $competitionId = Input::get("competition", 0);

        $marketTypes = array();
        if( $competitionId ) {
            $marketTypes = $this->marketTypeRepository->getMarketTypesForBaseCompetition($competitionId);
        } else {
            $marketTypes = $this->marketTypeRepository->findAll();
        }

        $marketOrdering = $this->marketOrderingService->getDefaultMarketTypes($competitionId);

        $competitions = $this->baseCompetitionRepository->findAll();

        return View::make('admin.eventdata.marketordering.index', compact('marketTypes', 'marketOrdering', 'competitions', 'competitionId'));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $competitionId = Input::get("competition", 0);
        $marketTypes = Input::get('market-types', array());

        $this->marketOrderingService->createOrUpdateForCompetition($marketTypes, $competitionId);

        return Redirect::route('admin.marketordering.index', array("competition" => $competitionId));

	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
