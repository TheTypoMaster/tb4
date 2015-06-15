<?php namespace TopBetta\Http\Controllers\Admin;

use Carbon\Carbon;
use Input;
use View;
use Redirect;
use Log;
use Queue;
use Config;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionResultRepositoryInterface;
use TopBetta\Services\Betting\EventService;
use TopBetta\Repositories\Contracts\EventModelRepositoryInterface;

class TournamentSportResultsController extends Controller {

    /**
     * @var EventModelRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var MarketRepositoryInterface
     */
    private $marketRepository;
    /**
     * @var EventService
     */
    private $eventService;
    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;
    /**
     * @var SelectionResultRepositoryInterface
     */
    private $selectionResultRepository;

    public function __construct(EventModelRepositoryInterface $eventRepository,
                                MarketRepositoryInterface $marketRepository,
                                EventService $eventService,
                                SelectionRepositoryInterface $selectionRepository,
                                SelectionResultRepositoryInterface $selectionResultRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->marketRepository = $marketRepository;
        $this->eventService = $eventService;
        $this->selectionRepository = $selectionRepository;
        $this->selectionResultRepository = $selectionResultRepository;
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
            $events = $this->eventRepository->searchSportEvents($search, 15);
        } else {
            $events = $this->eventRepository->getAllSportEvents(15);
        }

        return View::make('admin::tournaments.events.index', compact('events', 'search'));
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

        $event = $this->eventRepository->find($id);

        $tournamentMarketTypes = $event->competition->first()->tournamentMarketTypes->lists('id');

        $tournamentMarkets = $event->markets->filter(function($q) use ($tournamentMarketTypes) {
            return in_array($q->market_type_id, $tournamentMarketTypes);
        });

        $eventPaying = $this->eventService->isEventPaying($event);

        return View::make('admin::tournaments.events.edit', compact('event', 'tournamentMarkets', 'eventPaying', 'search'));
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

        $event = $this->eventRepository->find($id);

        $selectionResults = Input::get('market_results', array());

        foreach($selectionResults as $market => $selectionId) {
            if ( ! $selectionId ) {
                $this->marketRepository->updateWithId($market, array(
                    "market_status" => "R",
                ));

                $this->selectionResultRepository->deleteResultsForMarket($market);

                Queue::push('TopBetta\Services\Betting\MarketBetRefundingQueueService', array("market_id" => $market), Config::get('betresulting.queue'));
            }
        }

        $selections = $this->selectionRepository->findIn($selectionResults);
        foreach($selections as $selection) {

            if( $selection->market->result->count() && $selection->market->result->selection_id != $selection->id) {
                Log::info("Deleting result for selection " . $selection->market->result->selection_id . " market " . $selection->market->id);
                $selection->market->result->delete();
            }

            if( ! $selection->market->result->count() || $selection->market->result->selection_id != $selection->id ) {
                Log::info("Creating result for selection " . $selection->id . " market " . $selection->market->id);
                $this->selectionResultRepository->create(array(
                    "selection_id" => $selection->id,
                    "created_date" => Carbon::now(),
                ));
            }
        }

        $this->eventService->setEventPaying($event);
        Queue::push('TopBetta\Services\Betting\EventBetResultingQueueService', array('event_id' => $event->id), Config::get('betresulting.queue'));

        return Redirect::route('admin.tournament-sport-results.index', compact('search'))
            ->with(array("flash_message" => "Saved"));
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
