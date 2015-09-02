<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;

use Request;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface;
use TopBetta\Repositories\DbMarketRepository;
use View;
use Redirect;
use Input;

class MarketsController extends Controller
{

	/**
	 * @var DbMarketRepository
	 */
	private $marketsrepo;
    /**
     * @var MarketTypeRepositoryInterface
     */
    private $marketTypeRepository;
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    public function __construct(DbMarketRepository $marketsrepo, MarketTypeRepositoryInterface $marketTypeRepository, EventRepositoryInterface $eventRepository)
	{
		$this->marketsrepo = $marketsrepo;
        $this->marketTypeRepository = $marketTypeRepository;
        $this->eventRepository = $eventRepository;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Request::get('q', '');
        $event = Request::get('event', null);

		if ($search) {
			$markets = $this->marketsrepo->search($search);
		} else if ( $event ) {
            $markets = $this->marketsrepo->getAllMarketsForEvent($event);
        } else {
			$markets = $this->marketsrepo->allMarkets();
		}

        return View::make('admin.eventdata.markets.index', compact('markets', 'search', 'event'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        $search = Request::get('q', '');

        if( ! $event = Request::get('event_id', null) ) {
            return Redirect::back()
                ->with(array('flash_message' => "Please specify event"));
        }

        if( ! $event = $this->eventRepository->find($event) ) {
            return Redirect::back()
                ->with(array('flash_message' => "Please specify event"));
        }

        $marketTypes = $this->marketTypeRepository->findAll();

        return View::make('admin.eventdata.markets.create', compact('search', 'event', 'marketTypes'));

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $search = Request::get('q', '');

        if( ! $event = Request::get('event_id', null) ) {
            return Redirect::back()
                ->with(array('flash_message' => "Please specify event"));
        }

        if( ! $event = $this->eventRepository->find($event) ) {
            return Redirect::back()
                ->with(array('flash_message' => "Please specify event"));
        }

        $market = $this->marketsrepo->create(Request::except(array('_method', '_token', 'q')));

        return Redirect::route('admin.events.index', array('q' => $search))
            ->with(array('flash_message' => "Success"));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        //Get search string for filtering when redirecting after editing
		$search = Input::get("q", '');

		$market = $this->marketsrepo->findWithMarketTypePlusEvent($id);

        if (is_null($market)) {
            // TODO: flash message user not found
            return Redirect::route('admin.markets.index');
        }

        return View::make('admin.eventdata.markets.edit', compact('market', 'search'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        //Get search string for filtering when redirecting
		$search = Input::get('q', '');

		$data = Input::only('market_status', 'display_flag');
        $this->marketsrepo->updateWithId($id, $data);

        return Redirect::route('admin.markets.index', array($id, 'q'=>$search))
            ->with('flash_message', 'Saved!');
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
