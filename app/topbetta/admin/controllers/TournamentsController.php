<?php

namespace TopBetta\admin\controllers;

use Request;
use View;
use Input;
use TopBetta\Repositories\TournamentsRepo;
use TopBetta\Repositories\DbSportsRepository;
use TopBetta\Tournaments\TournamentCreation;

class TournamentsController extends \BaseController
{

	/**
	 * @var \TopBetta\Repositories\TournamentsRepo
	 */
	protected $tournamentRepo;
    protected $sportsrepo;
    protected $tournamentcreation;

	public function __construct(TournamentsRepo $tournamentRepo,
                                DbSportsRepository $sportsrepo,
                                TournamentCreation $tournamentcreation)
	{

		$this->tournamentRepo = $tournamentRepo;
        $this->sportsrepo = $sportsrepo;
        $this->tournamentcreation = $tournamentcreation;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Request::get('q', '');
		if ($search) {
			$tournaments = $this->tournamentRepo->search($search);
		} else {
			$tournaments = $this->tournamentRepo->allTournaments();
		}

		return View::make('admin::tournaments.index', compact('tournaments', 'search'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        $sports = $this->sportsrepo->selectList();
        return View::make('admin::tournaments.create', compact('sports'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $this->tournamentcreation->createFutureTournament(Input::All());
        $sports = $this->sportsrepo->selectList();
        return View::make('admin::tournaments.create', compact('sports'));
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
