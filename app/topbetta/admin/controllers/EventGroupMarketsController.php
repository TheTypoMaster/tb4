<?php

namespace TopBetta\admin\controllers;

use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use View;
use Input;
use Redirect;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;

class EventGroupMarketsController extends \BaseController {

    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;
    /**
     * @var MarketRepositoryInterface
     */
    private $marketRepository;

    public function __construct(CompetitionRepositoryInterface $competitionRepository, MarketRepositoryInterface $marketRepository)
    {
        $this->competitionRepository = $competitionRepository;
        $this->marketRepository = $marketRepository;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Input::get('q', '');

        if( $search ) {
            $competitions = $this->competitionRepository->search($search, true);
        } else {
            $competitions = $this->competitionRepository->findAllSportsCompetitions(15);
        }

        return View::make("admin::tournaments.sportmarkets.index", compact("competitions", "search"));
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
		//
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
        $search = Input::get('q', '');

        $competition = $this->competitionRepository->find($id);

        $markets = $this->marketRepository->getMarketsForCompetition($id);

        return View::make('admin::tournaments.sportmarkets.edit', compact('competition', 'markets' ,'search'));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $search = Input::get('q', '');

        $marketTypes = Input::get('market_types');

        $competition = $this->competitionRepository->find($id);

        $competition->tournamentMarketTypes()->sync($marketTypes);

        $competition->save();

        return Redirect::route('admin.tournament-sport-markets.index', array("q" => $search))
            ->with(array("flash_message" => "Saved!"));
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
