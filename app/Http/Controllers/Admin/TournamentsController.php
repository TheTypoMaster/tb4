<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;
use Carbon\Carbon;
use Request;
use TopBetta\Models\TournamentEventGroupModel;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\Repositories\Contracts\MeetingVenueRepositoryInterface;
use TopBetta\Repositories\Contracts\TODRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentBuyInRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentGroupRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentLabelsRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentPrizeFormatRepositoryInterface;
use TopBetta\Repositories\DbTournamentCompetiitonRepository;
use TopBetta\Repositories\DbTournamentRepository;
use TopBetta\Services\Tournaments\Exceptions\TournamentEntryException;
use TopBetta\Services\Tournaments\TournamentAdminService;
use TopBetta\Services\Tournaments\TournamentEventGroupService;
use TopBetta\Services\Tournaments\TournamentService;
use TopBetta\Services\Validation\Exceptions\ValidationException;
use View;
use Input;
use Redirect;
use TopBetta\Repositories\TournamentsRepo;
use TopBetta\Repositories\DbSportsRepository;

class TournamentsController extends Controller
{

	/**
	 * @var \TopBetta\Repositories\DbTournamentRepository
	 */
	protected $tournamentRepo;
    protected $sportsrepo;
    protected $tournamentcreation;
    /**
     * @var DbTournamentCompetiitonRepository
     */
    private $tournamentCompetiitonRepository;
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;
    /**
     * @var TournamentBuyInRepositoryInterface
     */
    private $buyInRepository;
    /**
     * @var TODRepositoryInterface
     */
    private $TODRepository;
    /**
     * @var TournamentLabelsRepositoryInterface
     */
    private $labelsRepository;
    /**
     * @var TournamentPrizeFormatRepositoryInterface
     */
    private $prizeFormatRepository;
    /**
     * @var TournamentService
     */
    private $tournamentService;
    /**
     * @var TournamentAdminService
     */
    private $tournamentAdminService;
    /**
     * @var MarketRepositoryInterface
     */
    private $marketRepository;
    /**
     * @var MeetingVenueRepositoryInterface
     */
    private $meetingVenueRepository;
    /**
     * @var TournamentGroupRepositoryInterface
     */
    private $tournamentGroupRepository;

    /**
     * @param DbTournamentRepository $tournamentRepo
     * @param DbSportsRepository $sportsrepo
     * @param DbTournamentCompetiitonRepository $tournamentCompetiitonRepository
     * @param CompetitionRepositoryInterface $competitionRepository
     * @param TournamentBuyInRepositoryInterface $buyInRepository
     * @param TODRepositoryInterface $TODRepository
     * @param TournamentLabelsRepositoryInterface $labelsRepository
     * @param TournamentPrizeFormatRepositoryInterface $prizeFormatRepository
     * @param TournamentService $tournamentService
     * @param TournamentAdminService $tournamentAdminService
     * @param MarketRepositoryInterface $marketRepository
     * @param MeetingVenueRepositoryInterface $meetingVenueRepository
     * @param TournamentGroupRepositoryInterface $tournamentGroupRepository
     */
    public function __construct(DbTournamentRepository $tournamentRepo,
                                DbSportsRepository $sportsrepo,
                                DbTournamentCompetiitonRepository $tournamentCompetiitonRepository,
                                CompetitionRepositoryInterface $competitionRepository,
                                TournamentBuyInRepositoryInterface $buyInRepository,
                                TODRepositoryInterface $TODRepository,
                                TournamentLabelsRepositoryInterface $labelsRepository,
                                TournamentPrizeFormatRepositoryInterface $prizeFormatRepository,
                                TournamentService $tournamentService,
                                TournamentAdminService $tournamentAdminService,
                                MarketRepositoryInterface $marketRepository,
                                MeetingVenueRepositoryInterface $meetingVenueRepository,
                                TournamentGroupRepositoryInterface $tournamentGroupRepository,
                                TournamentEventGroupService $tournamentEventGroupService)
	{

		$this->tournamentRepo = $tournamentRepo;
        $this->sportsrepo = $sportsrepo;

        $this->tournamentCompetiitonRepository = $tournamentCompetiitonRepository;
        $this->competitionRepository = $competitionRepository;
        $this->buyInRepository = $buyInRepository;
        $this->TODRepository = $TODRepository;
        $this->labelsRepository = $labelsRepository;
        $this->prizeFormatRepository = $prizeFormatRepository;
        $this->tournamentService = $tournamentService;
        $this->tournamentAdminService = $tournamentAdminService;
        $this->marketRepository = $marketRepository;
        $this->meetingVenueRepository = $meetingVenueRepository;
        $this->tournamentGroupRepository = $tournamentGroupRepository;
        $this->tournamentEventGroupService = $tournamentEventGroupService;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Request::get('q', '');
        $from = Request::get('from', null);
        $to = Request::get('to', null);
        $order = Request::get('order', array());

        //set the ordering
        if( count($order) ) {
            $this->tournamentRepo->setOrder($order);
        }

		if ($search) {
			$tournaments = $this->tournamentRepo->search($search);
		} else if ($from && $to) {
            $tournaments = $this->tournamentRepo->getTournamentsInDateRange($from, $to, 15);
        } else {
			$tournaments = $this->tournamentRepo->findAllPaginated();
		}
//        dd($tournaments);
		return View::make('admin.tournaments.index', compact('tournaments', 'search', 'from', 'to', 'order'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        $sports = array("Select Sport") + $this->sportsrepo->selectList();

        //get tournament comps
        $competitions = array("Select Competition");
        if($sportId = Input::old('tournament_sport_id')) {
            $competitionsCollection = $this->tournamentCompetiitonRepository->getBySport($sportId);

            if($competitionsCollection) {
                $competitions += $competitionsCollection->lists('name', 'id')->all();
            }
        }


        $eventGroups = $this->tournamentEventGroupService->getAllEventGroupsToArray();

        //get the buyins
        $buyins = array("Select Ticket Value");

        foreach($this->buyInRepository->findAll() as $buyin) {
            $buyins[$buyin->id] = $buyin->buy_in . ' + ' . $buyin->entry_fee;
        }

        //get tod venues
        $tod = array("Select Venue") + $this->TODRepository->findAll()->lists('venue', 'keyword')->all();

        //get tournament labels
        $labels = $this->labelsRepository->findAll()->lists('label', 'id')->all();

        //get prize formats
        $prizeFormats = $this->prizeFormatRepository->findAll()->lists('name', 'id')->all();

        $groups = $this->tournamentGroupRepository->findAll()->lists('group_name', 'id')->all();

        $venues = array("Select Meeting") + $this->meetingVenueRepository->findAll()->lists('name', 'id')->all();

        return View::make('admin.tournaments.create', compact('sports', 'buyins', 'tod', 'labels', 'prizeFormats', 'competitions', 'eventGroups', 'venues', 'groups'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $tournamentData = array_except(Input::all(), array(
            '_method',
            '_token',
            'entries_close_after',
            'type',
            'competition_id',
            'tournament_groups'
        ));

        //rebuy data
        if( ! Input::get('rebuys') ) {
            $tournamentData = array_except($tournamentData, array(
                'rebuys',
                'rebuy_currency',
                'rebuy_end',
                'tournament_rebuy_buyin_id',
                'rebuy_end_after'
            ));
        }

        //topup data
        if ( ! Input::get('topups') ) {
            $tournamentData = array_except($tournamentData, array(
                'topups',
                'topup_currency',
                'topup_end_date',
                'topup_start_date',
                'tournament_topup_buyin_id',
                'topup_end_after',
                'topup_start_after',
            ));
        }

        $tournamentData['tournament_sport_id'] = 0;
        try {
            $tournament = $this->tournamentService->createTournament($tournamentData);
        } catch (ValidationException $e) {
            return Redirect::route('admin.tournaments.create')->with(array('flash_message' => $e->getErrors()))->withInput();
        } catch (\Exception $e) {
            \Log::info($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return Redirect::route('admin.tournaments.create')->with(array('flash_message' => $e->getMessage()))->withInput();
        }

        return Redirect::route('admin.tournaments.index')->with(array('flash_message' => 'success'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$tournament = $this->tournamentRepo->find($id);

        return View::make('admin.tournaments.show', compact('tournament'));
	}

    public function downloadEntrants()
    {
        $tournament = Request::get('tournament_id');

        $tournament = $this->tournamentRepo->find($tournament)
            ->load(array('tickets.user.topbettauser'));

        //create csv
        $filename = '/tmp/tournament_'.$tournament->id.'.csv';
        $file = fopen($filename, 'w');
        fputcsv($file, array("id", 'username', 'name', 'email', 'mobile'));
        foreach ($tournament->tickets as $ticket) {
            fputcsv($file, array($ticket->user->id, $ticket->user->username, $ticket->user->name, $ticket->user->email, $ticket->user->topbettauser->msisdn));
        }

        //download
        return response()->download($filename)->deleteFileAfterSend(true);
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        $tournament = $this->tournamentRepo->find($id);

        $sports = array("Select Sport") + $this->sportsrepo->selectList();

        //get tournament comps
//        $competitions = array("Select Competition");
//        $competitionsCollection = $this->tournamentCompetiitonRepository->getBySport($tournament->sport->id);
//        if($competitionsCollection) {
//            $competitions += $competitionsCollection->lists('name', 'id')->all();
//        }
//        $tournament->competition_id = $tournament->eventGroup->tournament_competition_id;

        //get event groups
        $eventGroups = array("Select Event Group");
//        $eventGroupsCollection = $this->competitionRepository->getFutureEventGroupsByTournamentCompetition($tournament->eventGroup->id);
//        if($eventGroupsCollection) {
//            $eventGroups += $eventGroupsCollection->map(function($q) {
//                return array("id" => $q->id, 'name' => $q->name . ' - ' . $q->start_date);
//            })->lists('name', 'id')->all();
//        }
//        $eventGroups += array($tournament->eventGroup->id => $tournament->eventGroup->name . ' - ' . $tournament->eventGroup->start_date);

        $eventGroupsModel = $this->tournamentEventGroupService->getEventGroupByID($tournament->event_group_id);
        $eventGroups += array($tournament->event_group_id => $eventGroupsModel->name . ' - ' . $eventGroupsModel->start_date);

        //get the buyins
        $buyins = array("Select Ticket Value");

        foreach($this->buyInRepository->findAll() as $buyin) {
            $buyins[$buyin->id] = $buyin->buy_in . ' + ' . $buyin->entry_fee;
        }

        foreach($buyins as $buyinId => $buyin) {
            if( number_format($tournament->buy_in/100, 2) . ' + ' . number_format($tournament->entry_fee/100, 2) == $buyin){
                $tournament->tournament_buyin_id = $buyinId;
            }

            if( $tournament->rebuys && number_format($tournament->rebuy_buyin/100, 2) . ' + ' . number_format($tournament->rebuy_entry/100, 2) == $buyin){
                $tournament->tournament_rebuy_buyin_id = $buyinId;
            }

            if( $tournament->topups && number_format($tournament->topup_buyin/100, 2) . ' + ' . number_format($tournament->topup_entry/100, 2) == $buyin){
                $tournament->tournament_topup_buyin_id = $buyinId;
            }


        }

//        if ($tournament->sport->racing_flag) {
//            $parentTournaments = $this->tournamentRepo->findCurrentJackpotTournamentsByType('racing');
//        } else {
//            $parentTournaments = $this->tournamentRepo->findCurrentJackpotTournamentsByType('sport');
//        }

        $parentTournaments = $this->tournamentRepo->findCurrentJackpotTournamentsByType('racing');


        $parentTournaments = array(-1 => 'Select Tournament') + $parentTournaments->map(function($value){
            return array('id' => $value->id, 'name' => $value->name . ' - ' . $value->start_date . ' ($' .
                number_format($value->buy_in/100, 2) . ' + $' . number_format($value->entry_fee/100, 2) . ')');
        })->lists('name', 'id')->all();

        //get tod venues
        $tod = array("Select Venue") + $this->TODRepository->findAll()->lists('venue', 'keyword')->all();

        //get tournament labels
        $labels = $this->labelsRepository->findAll()->lists('label', 'id')->all();

        //get prize formats
        $prizeFormats = $this->prizeFormatRepository->findAll()->lists('name', 'id')->all();

        $groups = $this->tournamentGroupRepository->findAll()->lists('group_name', 'id')->all();

        return View::make('admin.tournaments.edit', compact('tournament', 'parentTournaments', 'sports', 'buyins', 'tod', 'labels', 'prizeFormats', 'competitions', 'eventGroups', 'groups'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $group = Input::get('tournament_groups');
        $tournamentData = array_except(Input::all(), array(
            '_method',
            '_token',
            'competition_id',
            'rebuy_end_after',
            'topup_start_after',
            'topup_end_after',
            'entries_close_after',
            'type',
            'competition_id',
            'tournament_groups'
        ));

        //rebuy data
        if( ! Input::get('rebuys') ) {
            $tournamentData = array_except($tournamentData, array(
                'rebuys',
                'rebuy_currency',
                'rebuy_end',
                'tournament_rebuy_buyin_id',
            ));
        }

        //topup data
        if ( ! Input::get('topups') ) {
            $tournamentData = array_except($tournamentData, array(
                'topups',
                'topup_currency',
                'topup_end_date',
                'topup_start_date',
                'tournament_topup_buyin_id',
            ));
        }

        try {
            $tournament = $this->tournamentService->updateTournament($id, $tournamentData);
        } catch (ValidationException $e) {
            return Redirect::route('admin.tournaments.edit', array($id))->with(array('flash_message' => $e->getErrors()))->withInput();
        } catch (\Exception $e) {
            \Log::info($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return Redirect::route('admin.tournaments.edit', array($id))->with(array('flash_message' => $e->getMessage()))->withInput();
        }

        return Redirect::route('admin.tournaments.index')->with(array('flash_message' => 'success'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
            $this->tournamentService->deleteTournament($id);
        } catch (\Exception $e) {
            return Redirect::route('admin.tournaments.index')
                ->with(array('flash_message' => $e->getMessage()));
        }

        return Redirect::route('admin.tournaments.index')
            ->with(array('flash_message' => "Tournament deleted"));
	}

    public function cancelForm($id)
    {
        $tournament = $this->tournamentRepo->find($id);

        if( ! $tournament ) {
            return Redirect::route('admin.tournaments.index')
                ->with(array('flash_message' => "Tournament not found"));
        }

        if( $tournament->paid_flag ) {
            return Redirect::route('admin.tournaments.index')
                ->with(array('flash_message' => "Tournament has already paid, cannot cancel"));
        }

        return View::make('admin.tournaments.cancel', compact('tournament'));
    }

    public function cancel($id)
    {
        try {
            $this->tournamentService->cancelTournament($id, Input::get('reason', null));
        } catch (\Exception $e) {
            return Redirect::route('admin.tournaments.index')
                ->with(array('flash_message' => $e->getMessage()));
        }

        return Redirect::route('admin.tournaments.index')
            ->with(array('flash_message' => "Tournament cancelled"));
    }

    /**
     * Form for adding user to tournament
     * @param $tournamentId
     * @return mixed
     */
    public function addUsersForm($tournamentId)
    {
        $tournament = $this->tournamentRepo->find($tournamentId);

        return View::make('admin.tournaments.add-users', compact('tournament'));
    }

    /**
     * Add users to tournament
     * @param $tournamentId
     * @return mixed
     */
    public function addUsers($tournamentId)
    {
        $users = Input::get('users');

        $result = array();
        if($users) {

            //get each username
            $users = explode(PHP_EOL, $users);

            //get rid of whitespace
            $users  = array_map('trim', $users);

            //enter the users
            try {
                $result = $this->tournamentAdminService->addUsersToTournamentByUsername($tournamentId, $users);
            } catch (TournamentEntryException $e) {
                return Redirect::to('/admin/tournaments/add-users', array($tournamentId))
                    ->with(array('flash_message' => $e->getMessage()));
            }
        }

        $tournament = $this->tournamentRepo->find($tournamentId);

        return View::make('admin.tournaments.add-users', compact('tournament', 'result'));
    }

    public function removeUserFromTournament($tournamentId, $userId)
    {
        try {
            $this->tournamentService->removeUserFromTournament($tournamentId, $userId);
        } catch(\Exception $e) {
            return Redirect::route('admin.tournaments.show', array($tournamentId))
                ->with(array("flash_message" => $e->getMessage()));
        }

        return Redirect::route('admin.tournaments.show', array($tournamentId))
            ->with(array('flash_message' => "User removed"));
    }

    /**
     * Ajax route for getting competitions by sport id
     * @param $sportId
     * @return array
     */
    public function getCompetitions($sportId)
    {
        $competitions = $this->tournamentCompetiitonRepository->getBySport($sportId);

        if( ! $competitions ) {
            return array("Select Competition");
        }

        return $this->formatForResponse(array("Select Competition") + $competitions->lists('name', 'id')->all());

    }

    public function getMarkets($competitionId)
    {
        $competition = $this->competitionRepository->find($competitionId);

        if($competition->sport_id <= 3) {
            return array();
        }

        $competitionMarkets = $competition->tournamentMarketTypes->lists('id')->all();
        $markets = $this->marketRepository->getMarketsForCompetition($competitionId)->toArray();

        foreach($markets as &$market) {
            if(in_array($market['market_type']['id'], $competitionMarkets)) {
                $market['tournament_status'] = true;
            } else {
                $market['tournament_status'] = false;
            }
        }

        return $markets;
    }

    public function getEventGroups($competitionId)
    {
        $eventGroups = $this->competitionRepository->getFutureEventGroupsByTournamentCompetition($competitionId);

        if( ! $eventGroups ) {
            return array("Select Event Group");
        }

        $eventGroups = $eventGroups->map(function($q) {
            return array("id" => $q->id, 'name' => $q->name . ' - ' . $q->start_date);
        });

        return $this->formatForResponse(array("Select Event Group") + $eventGroups->lists('name', 'id')->all());
    }

    /**
     * get event groups by type
     * @param $type_id
     * @return mixed
     */
    public function getEventGroupsByType($type_id) {
        return $this->tournamentEventGroupService->getEventGroupsByType($type_id);
    }

    public function getEvents($eventGroupId)
    {
        $eventGroup = $this->competitionRepository->find($eventGroupId);

        if( ! $eventGroup ) {
            return array("Select Event");
        }

        $events = array_map(function($value) {
            return array("id" => $value['id'], "name" => $value['number'] ? $value['number'] . '. ' . $value['name'] : $value['name'], "start_date" => $value['start_date']);
        }, $eventGroup->events()->get()->toArray());


        return array_merge(array(array("id"=> 0, "name" => "Select Event")), $events);
    }

    public function getParentTournaments($sportId)
    {
        $sport = $this->sportsrepo->find($sportId);

        if ($sport->isRacing()) {
            $tournaments = $this->tournamentRepo->findCurrentJackpotTournamentsByType('racing');
        } else {
            $tournaments = $this->tournamentRepo->findCurrentJackpotTournamentsByType('sport');
        }

        $tournaments = $tournaments->map(function($value){
            return array("id" => $value->id, "name" => $value->name . ' - ' . $value->start_date . ' ($' .
                number_format($value->buy_in/100, 2) . ' + $' . number_format($value->entry_fee/100, 2) . ')');
        });

        return $this->formatForResponse(array(-1 => "Select tournament") + $tournaments->lists('name', 'id')->all());

    }

    private function formatForResponse(array $array)
    {
        return array_map(function($key, $value) {
            return array("id" => $key, "name" => $value);
        }, array_keys($array), $array);
    }

}
