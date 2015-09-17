<?php

namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;
use View;
use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\Contracts\SportMarketTypeDetailsRepositoryInterface;

class SportMarketTypeDetailsController extends Controller
{

    /**
     * @var SportMarketTypeDetailsRepositoryInterface
     */
    private $marketTypeDetailsRepository;
    /**
     * @var SportRepositoryInterface
     */
    private $sportRepository;
    /**
     * @var MarketTypeRepositoryInterface
     */
    private $marketTypeRepository;

    public function __construct(SportMarketTypeDetailsRepositoryInterface $marketTypeDetailsRepository, SportRepositoryInterface $sportRepository, MarketTypeRepositoryInterface $marketTypeRepository)
    {
        $this->marketTypeDetailsRepository = $marketTypeDetailsRepository;
        $this->sportRepository = $sportRepository;
        $this->marketTypeRepository = $marketTypeRepository;
    }

    /**
     *
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $marketTypeDetails = $this->marketTypeDetailsRepository->findAllPaginated(array('sport', 'marketType'));

        return View::make('admin.eventdata.market-type-details.index', compact('marketTypeDetails'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $marketTypes = $this->marketTypeRepository->findAll();

        $sports = $this->sportRepository->findAll();

        return View::make('admin.eventdata.market-type-details.create', compact('sports', 'marketTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->only(array('sport_id', 'market_type_id', 'max_winning_selections'));

        if ($details = $this->marketTypeDetailsRepository->getBySportAndMarketType($data['sport_id'], $data['market_type_id'])) {
            $this->marketTypeDetailsRepository->updateWithId($details->id, array("max_winning_selections" => $data['max_winning_selections']));
        } else {
            $this->marketTypeDetailsRepository->create($data, 'market_type_id');
        }

        return \Redirect::route('admin.market-type-details.index')
            ->with(array('flash_message' => "Saved"));
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
        $detail = $this->marketTypeDetailsRepository->find($id);

        return View::make('admin.eventdata.market-type-details.edit', compact('detail'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        $data = $request->only(array('max_winning_selections'));

        $this->marketTypeDetailsRepository->updateWithId($id, $data);

        return \Redirect::route('admin.market-type-details.index')
            ->with(array('flash_message' => "Saved"));
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
