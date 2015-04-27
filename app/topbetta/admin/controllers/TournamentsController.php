<?php

namespace TopBetta\admin\controllers;

use Request;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentBuyInRepositoryInterface;
use TopBetta\Repositories\DbTournamentCompetiitonRepository;
use View;
use Input;
use TopBetta\Repositories\TournamentsRepo;
use TopBetta\Repositories\DbSportsRepository;

class TournamentsController extends \BaseController
{

	/**
	 * @var \TopBetta\Repositories\TournamentsRepo
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

    public function __construct(TournamentsRepo $tournamentRepo,
                                DbSportsRepository $sportsrepo,
                                DbTournamentCompetiitonRepository $tournamentCompetiitonRepository,
                                CompetitionRepositoryInterface $competitionRepository,
                                TournamentBuyInRepositoryInterface $buyInRepository)
	{

		$this->tournamentRepo = $tournamentRepo;
        $this->sportsrepo = $sportsrepo;

        $this->tournamentCompetiitonRepository = $tournamentCompetiitonRepository;
        $this->competitionRepository = $competitionRepository;
        $this->buyInRepository = $buyInRepository;
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
        $sports = array("Select Sport") + $this->sportsrepo->selectList();

        //get the buyins
        $buyins = array("Select Ticket Value");

        foreach($this->buyInRepository->findAll() as $buyin) {
            $buyins[$buyin->id] = $buyin->buy_in . ' + ' . $buyin->entry_fee;
        }

        return View::make('admin::tournaments.create', compact('sports', 'buyins'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        //$this->tournamentcreation->createFutureTournament(Input::All());
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

        return $this->formatForReponse(array("Select Competition") + $competitions->lists('name', 'id'));

    }

    public function getEventGroups($competitionId)
    {
        $eventGroups = $this->competitionRepository->getFutureEventGroupsByTournamentCompetition($competitionId);

        if( ! $eventGroups ) {
            return array("Select Event Group");
        }

        return $this->formatForReponse(array("Select Event Group") + $eventGroups->lists('name', 'id'));
    }

    private function formatForReponse(array $array)
    {
        return array_map(function($key, $value) {
            return array("id" => $key, "name" => $value);
        }, array_keys($array), $array);
    }

}
