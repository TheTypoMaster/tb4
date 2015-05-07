<?php

namespace TopBetta\admin\controllers;

use Carbon\Carbon;
use Request;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\TODRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentBuyInRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentLabelsRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentPrizeFormatRepositoryInterface;
use TopBetta\Repositories\DbTournamentCompetiitonRepository;
use TopBetta\Repositories\DbTournamentRepository;
use TopBetta\Services\Tournaments\TournamentAdminService;
use TopBetta\Services\Tournaments\TournamentService;
use TopBetta\Services\Validation\Exceptions\ValidationException;
use View;
use Input;
use Redirect;
use TopBetta\Repositories\TournamentsRepo;
use TopBetta\Repositories\DbSportsRepository;

class TournamentsController extends \BaseController
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
     * @param DbTournamentRepository $tournamentRepo
     * @param DbSportsRepository $sportsrepo
     * @param DbTournamentCompetiitonRepository $tournamentCompetiitonRepository
     * @param CompetitionRepositoryInterface $competitionRepository
     * @param TournamentBuyInRepositoryInterface $buyInRepository
     * @param TODRepositoryInterface $TODRepository
     * @param TournamentLabelsRepositoryInterface $labelsRepository
     * @param TournamentPrizeFormatRepositoryInterface $prizeFormatRepository
     * @param TournamentService $tournamentService
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
                                TournamentAdminService $tournamentAdminService )
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
			$tournaments = $this->tournamentRepo->findAllPaged();
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
        $sports = array("Select Sport") + $this->sportsrepo->selectList();

        //get tournament comps
        $competitions = array("Select Competition");
        if($sportId = Input::old('tournament_sport_id')) {
            $competitionsCollection = $this->tournamentCompetiitonRepository->getBySport($sportId);

            if($competitionsCollection) {
                $competitions += $competitionsCollection->lists('name', 'id');
            }
        }

        //get event groups
        $eventGroups = array("Select Event Group");
        if($competitionId = Input::old('competition_id')) {
            $eventGroupsCollection = $this->competitionRepository->getFutureEventGroupsByTournamentCompetition($competitionId);

            if($eventGroupsCollection) {
                $eventGroups += $eventGroupsCollection->lists('name', 'id');
            }
        }

        //get the buyins
        $buyins = array("Select Ticket Value");

        foreach($this->buyInRepository->findAll() as $buyin) {
            $buyins[$buyin->id] = $buyin->buy_in . ' + ' . $buyin->entry_fee;
        }

        //get tod venues
        $tod = array("Select Venue") + $this->TODRepository->findAll()->lists('venue', 'keyword');

        //get tournament labels
        $labels = $this->labelsRepository->findAll()->lists('label', 'id');

        //get prize formats
        $prizeFormats = $this->prizeFormatRepository->findAll()->lists('name', 'id');

        return View::make('admin::tournaments.create', compact('sports', 'buyins', 'tod', 'labels', 'prizeFormats', 'competitions', 'eventGroups'));
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
            'competition_id',
            'rebuy_end_after',
            'topup_start_after',
            'topup_end_after',
            'entries_close_after',
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
            $tournament = $this->tournamentService->createTournament($tournamentData);
        } catch (ValidationException $e) {
            return Redirect::route('admin.tournaments.create')->with(array('flash_message' => $e->getErrors()))->withInput();
        } catch (\Exception $e) {
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

    public function addUsersForm($tournamentId)
    {
        $tournament = $this->tournamentRepo->find($tournamentId);

        return View::make('admin::tournaments.add-users', compact('tournament'));
    }

    public function addUsers($tournamentId)
    {
        $users = Input::get('users');

        $result = array();
        if($users) {

            //get each username
            $users = explode(PHP_EOL, $users);

            //get rid of whitespace
            array_walk($users, 'trim');

            //enter the users
            $result = $this->tournamentAdminService->addUsersToTournamentByUsername($tournamentId, $users);
        }

        $tournament = $this->tournamentRepo->find($tournamentId);

        return View::make('admin::tournaments.add-users', compact('tournament', 'result'));
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

        return $this->formatForResponse(array("Select Competition") + $competitions->lists('name', 'id'));

    }

    public function getEventGroups($competitionId)
    {
        $eventGroups = $this->competitionRepository->getFutureEventGroupsByTournamentCompetition($competitionId);

        if( ! $eventGroups ) {
            return array("Select Event Group");
        }

        return $this->formatForResponse(array("Select Event Group") + $eventGroups->lists('name', 'id'));
    }

    public function getEvents($eventGroupId)
    {
        $eventGroup = $this->competitionRepository->find($eventGroupId);

        if( ! $eventGroup ) {
            return array("Select Event");
        }

        $events = array_map(function($value) {
            return array("id" => $value['id'], "name" => $value['name'], "start_date" => $value['start_date']);
        }, $eventGroup->events()->get()->toArray());


        return array_merge(array(array("id"=> 0, "name" => "Select Event")), $events);
    }

    public function getParentTournaments($sportId)
    {
        $sport = $this->sportsrepo->find($sportId);

        if ($sport->racing_flag) {
            $tournaments = $this->tournamentRepo->findCurrentTournamentsByType('racing');
        } else {
            $tournaments = $this->tournamentRepo->findCurrentTournamentsByType('sport');
        }

        return $this->formatForResponse(array("Select tournament") + $tournaments->lists('name', 'id'));

    }

    private function formatForResponse(array $array)
    {
        return array_map(function($key, $value) {
            return array("id" => $key, "name" => $value);
        }, array_keys($array), $array);
    }



}
