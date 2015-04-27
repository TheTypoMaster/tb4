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

    public function __construct(DbTournamentRepository $tournamentRepo,
                                DbSportsRepository $sportsrepo,
                                DbTournamentCompetiitonRepository $tournamentCompetiitonRepository,
                                CompetitionRepositoryInterface $competitionRepository,
                                TournamentBuyInRepositoryInterface $buyInRepository,
                                TODRepositoryInterface $TODRepository,
                                TournamentLabelsRepositoryInterface $labelsRepository,
                                TournamentPrizeFormatRepositoryInterface $prizeFormatRepository)
	{

		$this->tournamentRepo = $tournamentRepo;
        $this->sportsrepo = $sportsrepo;

        $this->tournamentCompetiitonRepository = $tournamentCompetiitonRepository;
        $this->competitionRepository = $competitionRepository;
        $this->buyInRepository = $buyInRepository;
        $this->TODRepository = $TODRepository;
        $this->labelsRepository = $labelsRepository;
        $this->prizeFormatRepository = $prizeFormatRepository;
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
			$tournaments = $this->tournamentRepo->findAllPaginated();
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

        //get event roups
        $eventGroups = array("Select Event Group");
        if($competitionId = Input::old('competition_id')) {
            $eventGroupsCollection = $this->competitionRepository->getFutureEventGroupsByTournamentCompetition($competitionId);

            if($eventGroupsCollection) {
                $eventGroups += $eventGroupsCollection->lists('name', 'id');
            }
        }

        $events = array();

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

        return View::make('admin::tournaments.create', compact('sports', 'buyins', 'tod', 'labels', 'prizeFormats', 'competitions', 'eventGroups', 'events'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $tournamentData = array();
        //tournament data
        $tournamentData["tournament_sport_id"] = Input::get("tournament_sport_id");
        $tournamentData["event_group_id"] = Input::get("event_group_id");
        $tournamentData["jackpot_flag"] = Input::get("jackpot_flag");
        $tournamentData["start_currency"] = Input::get("start_currency");
        $tournamentData["tod_flag"] = Input::get("tod_flag") ?: '';
        $tournamentData["status_flag"] = Input::get("status_flag");
        $tournamentData["minimum_prize_pool"] = Input::get("minimum_prize_pool");
        $tournamentData["free_credit_flag"] = Input::get("free_credit_flag");
        $tournamentData["tournament_prize_format"] = Input::get("tournament_prize_format");
        $tournamentData["closed_betting_on_first_match_flag"] = Input::get("closed_betting_on_first_match_flag", false);
        $tournamentData["reinvest_winnings_flag"] = Input::get("reinvest_winnings_flag", false);
        $tournamentData["bet_limit_flag"] = Input::get("bet_limit_flag", false);
        $tournamentData["bet_limit_per_event"] = Input::get("bet_limit_per_event") ? Input::get("bet_limit_per_event")*100 : null;
        $tournamentData["entries_close"] = Input::get("entries_close");
        $tournamentData["tournament_sponsor_name"] = Input::get("tournament_sponsor_name");
        $tournamentData["tournament_sponsor_logo"] = Input::get("tournament_sponsor_logo");
        $tournamentData["tournament_sponsor_logo_link"] = Input::get("tournament_sponsor_logo_link");
        $tournamentData["parent_tournament_id"] = Input::get("parent_tournament_id");
        $tournamentData['created_date'] = Carbon::now()->toDateTimeString();
        $tournamentData['updated_date'] = Carbon::now()->toDateTimeString();

        //get buyin info
        if( ! $buyin = Input::get('tournament_buyin_id') ) {
            return Redirect::to('admin.tournaments.create')->with(array('flash_message' => "Please specify buyin amount"))->withInput();
        }

        $buyin = $this->buyInRepository->find($buyin);
        $tournamentData['buy_in'] = $buyin->buy_in * 100;
        $tournamentData['entry_fee'] = $buyin->entry_fee * 100;

        //get tournament name and desc
        $tournamentData['name'] = $this->generateTournamentAutomatedText('name', $tournamentData);
        $tournamentData['description'] = $this->generateTournamentAutomatedText('description', $tournamentData);

        //convert from cents
        $tournamentData['start_currency'] *= 100;
        $tournamentData['minimum_prize_pool'] *= 100;

        //get start and end dates
        if( ! $eventGroup = Input::get('event_group_id') ) {
            return Redirect::route('admin.tournaments.create')->with(array('flash_message' => "Please specify event group"))->withInput();
        }

        if($event = $this->competitionRepository->getFirstEventForCompetition($eventGroup) ) {
            $tournamentData['start_date'] = $event->start_date;
            $tournamentData['end_date']   = $this->competitionRepository->getLastEventForCompetition($eventGroup)->start_date;
        } else {
            $tournamentData['start_date'] = $this->competitionRepository->find($eventGroup)->start_date;
            $tournamentData['end_date'] = $this->competitionRepository->find($eventGroup)->start_date;
        }
        if( Input::get('close_betting_on_first_match_flag') ) {
            $tournamentData['betting_closed_date'] = $tournamentData['start_date'];
        } else {
            $tournamentData['betting_closed_date'] = $tournamentData['end_date'];
        }

        //tournament of the day
        $tod = Input::get('tod_flag');
        if ( $tod && $this->tournamentRepo->tournamentOfTheDay($tod, Carbon::createFromFormat('Y-m-d H:i:s', $tournamentData['start_date'])->toDateString()) ) {
            return Redirect::route('admin.tournaments.create')->with(array('flash_message' => 'Tournament of the day already exists'))->withInput();
        }

        //rebuyInfo
        if( Input::get('rebuys') ) {
            try {
                $rebuys = $this->processRebuys();
            } catch (\Exception $e) {
                return Redirect::route('admin.tournaments.create')->with(array('flash_message' => $e->getMessage()))->withInput();
            }

            $tournamentData = array_merge($tournamentData, $rebuys);
        }

        //rebuyInfo
        if( Input::get('topup_flag') ) {
            try {
                $topups = $this->processTopUps();
            } catch (\Exception $e) {
                return Redirect::route('admin.tournaments.create')->with(array('flash_message' => $e->getMessage()))->withInput();
            }
            $tournamentData = array_merge($tournamentData, $topups);
        }

        //create the tournament
        try {
            $tournament = $this->tournamentRepo->create($tournamentData);
        } catch (ValidationException $e) {
            return Redirect::route('admin.tournaments.create')->with(array('flash_message' => $e->getErrors()));
        }

        $tournament = $this->tournamentRepo->find($tournament['id']);

        //add labels
        if( $labels = Input::get('tournament_labels') ) {
            $tournament->tournamentlabels()->sync($labels);
        }

        return Redirect::route('admin.tournaments.index');
	}

    private function processRebuys()
    {
        $buyinData = array();

        $buyinData['rebuys'] = Input::get('rebuys');
        $buyinData['rebuy_currency'] = Input::get('rebuy_currency');

        //rebuy end date
        if ( ! Input::get('rebuy_end') ) {
            throw new \Exception("Please specify rebuy end date");
        }

        $buyinData['rebuy_end'] = Input::get('rebuy_end');

        //get buyin info
        if( ! $buyin = Input::get('tournament_rebuy_buyin') ) {
            throw new \Exception("Please specify rebuy buyin amount");
        }

        $buyin = $this->buyInRepository->find($buyin);
        $buyinData['rebuy_buyin'] = $buyin->buy_in * 100;
        $buyinData['rebuy_entry'] = $buyin->entry_fee * 100;

        return $buyinData;
    }
    
    private function processTopUps()
    {
        $topupData = array();
        $topupData['topup_flag'] = Input::get('topup_flag');
        $topupData['topup_currency'] = Input::get('topup_currency');

        //topup start date
        if ( ! Input::get('topup_start_date') ) {
            throw new \Exception("Please specify topup start date");
        }
        $topupData['topup_start_date'] = Input::get('topup_start_date');

        //topup end date
        if ( ! Input::get('topup_end_date') ) {
            throw new \Exception("Please specify topup end date");
        }
        $topupData['topup_end_date'] = Input::get('topup_end_date');

        //get topup info
        if( ! $topup = Input::get('tournament_topup_buyin') ) {
            throw new \Exception("Please specify topup buyin amount");
        }

        $topup = $this->buyInRepository->find($topup);
        $topupData['topup_buyin'] = $topup->buy_in * 100;
        $topupData['topup_entry'] = $topup->entry_fee * 100;

        return $topupData;
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

    /**
     * Borrowed from legacy administrator
     * @param $field
     * @return string
     */
    private function generateTournamentAutomatedText($field, $tournamentData)
    {
        $jackpot_flag			= array_get($tournamentData, 'jackpot_flag', 0);
        $parent_tournament_id	= array_get($tournamentData, 'parent_tournament_id', null);
        $minimum_prize_pool		= array_get($tournamentData, 'minimum_prize_pool', 0);


        $reinvest_winnings_flag = array_get($tournamentData, 'reinvest_winnings_flag', 0);
        $closed_betting_on_first_match_flag = array_get($tournamentData, 'closed_betting_on_first_match_flag', 0);
        $tournament_sponsor_name = array_get($tournamentData, 'tournament_sponsor_name', null);


        $buyin_amount				= number_format(array_get($tournamentData, 'buy_in', 0)/100, 2);
        $minimum_prize_pool_amount	= number_format($minimum_prize_pool, 2);
        $free_credit_flag 			= (int)array_get($tournamentData, 'free_credit_flag', 0);

        $automated_text = '';
        $tournamntType = '';

        switch ($field) {
            case 'name':
                $meeting_id				= array_get($tournamentData, 'event_group_id', -1);
                $event_id				= array_get($tournamentData, 'event_id', -1);
                $future_meeting_venue	= array_get($tournamentData, 'future_meeting_venue', -1);

                if (!empty($meeting_id) && $meeting_id != -1) {
                    $meeting	= $this->competitionRepository->find($meeting_id);
                    $automated_text	.= $meeting->name ;
                } else if (!empty($future_meeting_venue) && $future_meeting_venue != -1) {
                    $automated_text .= $future_meeting_venue;
                }
                // $automated_text .= ($buyin->buy_in > 0 ? ' $' . $buyin_amount : ' FREE');

                //	if (!$jackpot_flag) {
                //		$automated_text .= '/' . $minimum_prize_pool_amount;
                //	}

                break;
            case 'description':
                if ($jackpot_flag) {
                    $tournamntType = 'jackpot';
                } elseif ($free_credit_flag){
                    $tournamntType = 'free credit';
                }else {
                    $tournamntType = 'cash';
                }
                $automated_text  = 'This is a ' . $tournamntType . ' tournament.';
                $automated_text .= ' The cost of entry is ';

                if ($buyin_amount > 0) {
                    $automated_text .= '$' . $buyin_amount . ' + $' . number_format(array_get($tournamentData, 'entry_fee',0)/100, 2) . '.';
                } else {
                    $automated_text .= 'Free.';

                }

                if ($closed_betting_on_first_match_flag == 1){
                    $automated_text .= ' You can not bet after the 1st event in this tournament starts.';
                }

                if ($reinvest_winnings_flag == 0 && $closed_betting_on_first_match_flag != 1){
                    $automated_text .= ' You can not re-invest your winnings in this tournament.';
                }

                $automated_text .= ' Winners will receive';

                if (empty($jackpot_flag) || -1 == $parent_tournament_id) {
                    $automated_text .= ' a share of a guaranteed $' . $minimum_prize_pool_amount;
                    if($free_credit_flag){
                        $automated_text .= ' in free credit.';
                    } else {
                        $automated_text .= '.';
                    }

                    if ($buyin_amount > 0) {
                        $automated_text .= ' Once the minimum is reached, the prize pool will continue to grow by $' . $buyin_amount . ' per entrant.';
                    }
                } else {
                    $parent_tournament	= $this->tournamentRepo->find($parent_tournament_id);
                    $start_date_time	= strtotime($parent_tournament->start_date);

                    $automated_text .= ' a ticket into the ' . $parent_tournament->name;
                    $automated_text .= ' tournament on ' . date('D', $start_date_time) . ' ' . date('jS F', $start_date_time) . '.';

                    if ($buyin_amount == 0) {

                        $ticket_count	= floor($minimum_prize_pool * 100 / ($parent_tournament->entry_fee + $parent_tournament->buy_in));

                        if($ticket_count > 1) {
                            $automated_text .= ' There are ' . $ticket_count . ' tickets to be won.';
                        } else {
                            $automated_text .= ' There is ' . $ticket_count . ' ticket to be won.';
                        }
                    } else {
                        $automated_text .= ' The Number of tickets awarded will depend on the number of entrants.';
                    }
                }

                $automated_text .= "\n\nGood luck and good punting!";

                break;
        }

        return $automated_text;
    }

}
