<?php

namespace TopBetta\admin\controllers;

use View;
use TopBetta\Repositories\Contracts\EventModelRepositoryInterface;

class TournamentSportResultsController extends \BaseController {

    /**
     * @var EventModelRepositoryInterface
     */
    private $eventRepository;

    public function __construct(EventModelRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$events = $this->eventRepository->getAllSportEvents(15);

        return View::make('admin::tournaments.events.index');
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
		$event = $this->eventRepository->find($id);

        return View::make('admin::tournaments.events.edit', compact('event'));
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
