<?php namespace TopBetta\Http\Controllers\Frontend;

use TopBetta\Http\Controllers\Controller;
use TopBetta\Helpers\TimeHelper;
use TopBetta\Repositories\DbTournamentLeaderboardRepository;
use Illuminate\Support\Facades\Input;
use TopBetta\Services\Tournaments\TournamentLeaderboardService;


class FrontTournamentsController extends Controller {

    protected $tournamentleaderboard;
    /**
     * @var TournamentLeaderboardService
     */
    private $tournamentLeaderboardService;

    function __construct(DbTournamentLeaderboardRepository $tournamentleaderboard, TournamentLeaderboardService $tournamentLeaderboardService) {
        $this->tournamentleaderboard = $tournamentleaderboard;
        $this->tournamentLeaderboardService = $tournamentLeaderboardService;
    }


	protected $racingMap = array('r' => 'galloping', 'g' => 'greyhounds', 'h' => 'harness');

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		// handle our affiliate stuff if set
		$affiliateId = Input::get('aff_id', null);
		$campaignId = Input::get('cam_id', null);

		//
		$type = Input::get('type', 'racing');

		// special case for the atp landing page
		// new feature being implemented will replace this
		if ($type == 'atp-landing') {
			
			$affiliateId = 'G01';
			$campaignId = 'ATP';

			/*
			$featuredTourns = array(
				'2013-10-12' => array(
					'free' => 53737, 
					'paid' => 53757), 
				'2013-10-16' => array(
					'free' => 53739, 
					'paid' => 53757),
				'2013-10-19' => array(
					'free' => 53741, 
					'paid' => 53759), 
				'2013-10-20' => array(
					'free' => 53743, 
					'paid' => 53759), 
				'2013-10-25' => array(
					'free' => 53745, 
					'paid' => 53759), 
				'2013-10-26' => array(
					'free' => 53747, 
					'paid' => 53761), 
				'2013-11-02' => array(
					'free' => 53749, 
					'paid' => 53763), 
				'2013-11-05' => array(
					'free' => 53751, 
					'paid' => 53763), 
				'2013-11-07' => array(
					'free' => 53753, 
					'paid' => 53763)
				);

			echo serialize($featuredTourns);exit;
			*/

			$featuredTourns = \TopBetta\Models\Affiliates::where('affiliate_id', $affiliateId)->where('campaign_id', $campaignId)->value('filter');
			if ($featuredTourns) {
				$featuredTourns = unserialize($featuredTourns);
			} else {
				$featuredTourns = array();
			}	
			
			// we reverse the order and find the first match :-)
			$value = array_first(array_reverse($featuredTourns), function($key, $value)
			{
				$date = new \DateTime();
				$date = $date -> format('Y-m-d');

				// start with the first one if we are not up to the start date yet
				$date = ($date < '2013-10-12') ? '2013-10-12' : $date;   

			    return $key <= $date;
			});			

			return array("success" => true, "result" => $value);

		}

		//sub type e.g. for racing: greyhounds, for sports: nrl
		$sub_type = Input::get('sub_type', null);

		//remap our subtype to the legacy keyword
		if ($sub_type) {

			if (array_key_exists($sub_type, $this -> racingMap)) {

				$sub_type = $this -> racingMap[$sub_type];

			}

		}

		//do we want a filtered list for the user joined tournaments only
		$entered = Input::get('entered', false);

		if ($entered) {

			// special case to clear my tournaments after a user has been logged in
			if (\Auth::guest()) return array("success" => true, "result" => array());

			//we want to see all entered tournaments for the entered list
			$type = null;

		}

		$tournamentModel = new \TopBetta\Models\Tournament;

		$list_params = array('jackpot' => false, 'private' => 0, 'type' => $type, 'sub_type' => $sub_type);

		$tournamentList = $tournamentModel -> getTournamentActiveList($list_params);			

		$filterList = false;

		// filter for affiliate only tournaments
		if ($affiliateId && $campaignId && !$entered) {
			$filter = \TopBetta\Models\Affiliates::where('affiliate_id', $affiliateId)->where('campaign_id', $campaignId)->value('filter');
			if ($filter) {
				$filter = unserialize($filter);

				$whitelistTournIds = false;
				$whitelistSportIds = false;
				$noWhitelist = true;

				if (array_key_exists("whitelist",$filter)) {
					$noWhitelist = false;
					$whitelistTournIds = $filter['whitelist']['tournament_ids'];
					$whitelistSportIds = $filter['whitelist']['tournament_sports'];
				}

				$filterList = array();

				// ** AT THIS STAGE WE CAN ONLY WORK WITH WHITELIST TOURN IDS >>OR<< WHITELIST SPORTS
				if ($whitelistTournIds) {
					foreach ($whitelistTournIds as $id) {
					   $object = new \stdClass();
					   $object->id = $id;				
					   $filterList[] = $object;
					}			
				}

				if ($whitelistSportIds) {		
					foreach ($whitelistSportIds as $id) {
						foreach ($tournamentList as $tournament) {
							if ($tournament->tournament_sport_id == $id) {
							   $object = new \stdClass();
							   $object->id = $tournament->id;				
							   $filterList[] = $object;									
							}
						}
					}
				}

				if (!$filterList && !$noWhitelist) {
					//they have no tournaments
					return array("success" => true, "result" => array());
				}
			}	
		} 

		// filter for logged in users tournaments they have entered only - "My Tourns"
		if (\Auth::check() && $entered && !$filterList) {

			$filterList = $tournamentModel -> getMyTournamentListByUserID(\Auth::user() -> id, false, false, true);

			if (!$filterList) {

				//they have no tournaments
				return array("success" => true, "result" => array());

			}

		}

		// >>>>>>>>>>>>>>>>>> START TEMP ATP
//		$affiliateId = 'G01';
//		$campaignId = 'ATP2';
		
		/*
		$atpTournaments = array(
			'53737' => array('atp'),
			'53685' => array('atp'),
			'53739' => array('atp'),
			'53721' => array('atp'),
			'53741' => array('atp'),
			'53723' => array('atp'),
			'53743' => array('atp'),
			'53725' => array('atp'),
			'53745' => array('atp'),
			'53727' => array('atp'),
			'53747' => array('atp'),
			'53729' => array('atp'),
			'53749' => array('atp'),
			'53731' => array('atp'),
			'53751' => array('atp'),
			'53733' => array('atp'),
			'53753' => array('atp'),
			'53735' => array('atp'),
			'53719' => array('atp','final')
		);

		echo serialize($atpTournaments);exit;
		/*/

//		$atpTournaments = \TopBetta\Affiliates::where('affiliate_id', $affiliateId)->where('campaign_id', $campaignId)->value('filter');
//		if ($atpTournaments) {
//			$atpTournaments = unserialize($atpTournaments);
//		} else {
//			$atpTournaments = array();
//		}
		// <<<<<<<<<<<<<<<<< END TEMP ATP

		$meetingId = NULL;
		$eachMeeting = array();

		// loop through every meeting
		foreach ($tournamentList as $tournament) {

			//TODO: bad code for now - had to live code on the server - can't get races to load on my f*n pc!!
			if ($filterList) {

				$keep = 0;
				foreach ($filterList as $myId) {

					if ($tournament -> id == $myId -> id) {
						$keep++;
					}
				}

				if ($keep == 0) {
					continue;
				}
			}

			$numTournaments = 0;

			if ($meetingId != $tournament -> event_group_id) {

				$meetingName = $tournament -> event_group_name;
				$meetingId = $tournament -> event_group_id;
				
				$nextEventStartTime = \TopBetta\Models\Tournament::getNextEventStartTimeForEventGroupId($meetingId);
                ($nextEventStartTime) ? $nextEventStartTime = \TopBetta\Helpers\TimeHelper::isoDate($nextEventStartTime) : $nextEventStartTime = \TopBetta\Helpers\TimeHelper::isoDate($tournament->start_date);
                // $nextEventStartTime = \TopBetta\Helpers\TimeHelper::isoDate($nextEventStartTime);

				$tourns = array();

				// build our list of tournaments for this meeting
				foreach ($tournamentList as $tourn) {
					//TODO: bad code for now - had to live code on the server - can't get races to load on my f*n pc!!
					if ($filterList) {

						$keep = 0;
						foreach ($filterList as $myId) {

							if ($tourn -> id == $myId -> id) {
								$keep++;
							}
						}

						if ($keep == 0) {
							continue;
						}
					}

					if ($tourn -> event_group_id == $meetingId) {

						$numTournaments++;

						//calculate prize pool
						$prizePool = $tournamentModel -> calculateTournamentPrizePool($tourn -> id);

						//fetch num entries
						$numEntries = \TopBetta\Models\TournamentTicket::countTournamentEntrants($tourn -> id);

						//fetch places paid
						$placesPaid = $tournamentModel -> calculateTournamentPlacesPaid($tourn, $numEntries, $prizePool);

						//convert the date to ISO 8601 format
						$startDatetime = \TopBetta\Helpers\TimeHelper::isoDate($tourn -> start_date);
						$endDatetime = \TopBetta\Helpers\TimeHelper::isoDate($tourn -> end_date);

						// TEMP FOR ATP
						$labels = array();
//						if (array_key_exists($tourn -> id, $atpTournaments)) {
//							$labels = $atpTournaments[$tourn -> id];
//						}

                        // TEMP for tournament landing page until proper tournament group/labels are implimented
                        ($tourn->featured == "Featured") ? $featuredTournamentFlag = true : $featuredTournamentFlag = false;

                       // ($tourn->tournament_sponsor_name) ? $tournamentName = $tourn->name . ' - '.$tourn->tournament_sponsor_name  : $tournamentName = $tourn->name;

                        /// if tournament has a entry close time and it's before teh start time then set the start date to that
                        if($tourn->entries_close != '0000-00-00 00:00:00' && $tourn->entries_close < $tourn->start_date ){
                            $startDatetime = \TopBetta\Helpers\TimeHelper::isoDate($tourn->entries_close);
                        }

						$tourns[] = array('id' => (int)$tourn -> id, 'name' => $tourn->name, 'buy_in' => (int)$tourn -> buy_in, 'entry_fee' => (int)$tourn -> entry_fee, 'num_entries' => (int)$numEntries, 'prize_pool' => (int)$prizePool, 'places_paid' => (int)$placesPaid, 'start_currency' => $tourn -> start_currency, 'bet_limit_flag' => $tourn->bet_limit_flag, 'start_date' => $startDatetime, 'end_date' => $endDatetime, 'labels' => $labels, 'featured' => $featuredTournamentFlag, 'tournament_sponsor_name' => $tourn->tournament_sponsor_name, 'tournament_sponsor_logo' => $tourn->tournament_sponsor_logo, 'tournament_sponsor_logo_link' => $tourn->tournament_sponsor_logo_link, 'reinvest_winnings_flag' => $tourn->reinvest_winnings_flag, 'closed_betting_on_first_match_flag' => $tourn->closed_betting_on_first_match_flag,
                            //rebuy info
                              'rebuys' => $tourn->rebuys,
                              'rebuy_currency' => $tourn->rebuy_currency,
                              'rebuy_entry' => $tourn->rebuy_entry,
                              'rebuy_buyin' => $tourn->rebuy_buyin,
                              'rebuy_end' => $tourn->rebuy_end,

                            //topup info
                              'topups' => $tourn->topups,
                              'topup_currency' => $tourn->topup_currency,
                              'topup_entry' => $tourn->topup_entry,
                              'topup_buyin' => $tourn->topup_buyin,
                              'topup_end_date' => $tourn->topup_end_date,
                              'topup_start_date' => $tourn->topup_start_date,
                        );
					}

					//handle sub_type for racing
					$flipRacingMap = array_flip($this -> racingMap);

					if (array_key_exists($tournament -> sport_name, $flipRacingMap)) {

						$sub_type_name = $flipRacingMap[$tournament -> sport_name];
						$tournamentType = 'r';

					} else {

						$sub_type_name = $tournament -> sport_name;
						$tournamentType = 's';

					}

				}

                //($tournament->tournament_sponsor_name) ? $meetingNameWithTournSponsor = $meetingName. ' - '. $tournament->tournament_sponsor_name: $meetingNameWithTournSponsor = $meetingName;
				$eachMeeting[] = array('id' => (int)$meetingId, 'name' => $meetingName, 'next_event_start' => $nextEventStartTime, 'state' => $tournament -> state, 'weather' => $tournament -> weather, 'track' => $tournament -> track, 'num_tournaments' => $numTournaments, 'sub_type' => $sub_type_name, 'tournament_type' => $tournamentType, 'tournaments' => $tourns);
			}
		}

		return array("success" => true, "result" => $eachMeeting);

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($tournamentId, $groupedStatus = false) {
		//TODO: work with private tournaments
		//TODO: work with jackpot tournaments

		$grouped = \Input::get('grouped', $groupedStatus);

		//does tournament exist?
		$tournamentModel = new \TopBetta\Models\Tournament;
		$tournament = $tournamentModel -> find($tournamentId);

		if (is_null($tournament)) {

			return array('success' => false, 'error' => \Lang::get('tournaments.not_found', array('tournamentId' => $tournamentId)));

		}

		//is this a racing or sports tournament
		$isRacingTournament = $tournamentModel -> isRacing($tournamentId);

		//looks like racing uses meeting_id & sports uses event_group_id ???
		//as the event_group_id
		$meetingId = $tournament -> event_group_id;

		if (!$isRacingTournament) {

			//get the comp_id and events list
			$eventGroup = \TopBetta\Models\SportsComps::find($meetingId);

			$eventList = \TopBetta\Models\SportsEvents::where('event_group_id', '=', $meetingId) -> get();

			$events = array();

			foreach ($eventList as $event) {

				$events[] = (int)$event -> event_id;

			}

		}

		//get entries/player list
		$ticketModel = new \TopBetta\Models\TournamentTicket;

		$playerList = \Cache::remember("tournament-$tournamentId-userlist", 5, function() use ($ticketModel, $tournamentId) {
			return $ticketModel -> getTournamentEntrantList($tournamentId);
		});

		//leaderboard
		// $leaderboardModel = new \TopBetta\Models\TournamentLeaderboard;
        $leaderboard = array();

		if (strtotime($tournament -> start_date) < time()) {
            // dirty controller business logic....
            if ($tournament -> paid_flag) {

				$leaderboard = $this->tournamentLeaderboardService->getLeaderboard($tournament->id, 50, true);
			} else {

                $leaderboard = $this->tournamentLeaderboardService->getLeaderboard($tournament->id, 50, false);
            }


//			if ($tournament -> paid_flag) {
//
//				$leaderboard = $leaderboardModel -> getLeaderBoardRank($tournament, 50, true);
//
//			} else {
//
//				$leaderboard = $leaderboardModel -> getLeaderBoardRank($tournament, 50);
//
//			}
//
//			foreach ($leaderboard as $id => $val) {
//				$leaderboard[$id] -> id = (int)$leaderboard[$id] -> id;
//				$leaderboard[$id] -> currency = (int)$leaderboard[$id] -> currency;
//				$leaderboard[$id] -> qualified = ($leaderboard[$id] -> qualified == 0) ? false : true;
//
//				// we don't really need these showing
//				unset($leaderboard[$id] -> name);
//				unset($leaderboard[$id] -> turned_over);
//			}

		}

		//get prize pool in cents & places paid
        \Cache::forget("tournament-$tournamentId-prizepool");
        $prizePool = \Cache::remember("tournament-$tournamentId-prizepool", 5, function() use ($tournamentModel, $tournamentId) {
            return $tournamentModel -> calculateTournamentPrizePool($tournamentId);
        });

        $placeList = \Cache::remember("tournament-$tournamentId-placelist", 5, function() use ($tournamentModel, $tournament, $playerList, $prizePool) {
            return $tournamentModel -> calculateTournamentPlacesPaid($tournament, count($playerList), $prizePool);
        });

        if ($tournament -> free_credit_flag && $placeList) {

            $placeList['formula'] = "freecredit";

        }

        //work out places paid via place list
        $places_paid = 0;
        if ($placeList) {
            foreach ($placeList['place'] as $place => $prize) {
                $place_display[$place] = array();
                if (isset($prize['ticket']) && !empty($prize['ticket'])) {
                    $place_display[$place][] = '1 Ticket (#' . $prize['ticket'] . ')';
                }

                if (isset($prize['cash']) && !empty($prize['cash'])) {
                    $place_display[$place][] = $prize['cash'];
                }

                // little attempt at making a free credit prize pool
                if ($tournament -> free_credit_flag && isset($prize['cash']) && !empty($prize['cash'])) {

                    $placeList['place'][$place]['freecredit'] = $prize['cash'];
                    unset($placeList['place'][$place]['cash']);

                }

                $place_display[$place] = join(' + ', $place_display[$place]);
            }
            $places_paid = count($place_display);
        }

        $numRegistrations = count($playerList);

        // TEMP for tournament landing page until proper tournament group/labels are implimented
        (\TopBetta\Models\Tournament::isTournamentFeatured($tournament->id)) ? $featuredTournamentFlag = true : $featuredTournamentFlag = false;

        //calculate tournament end date/betting open

        // special case to send data back in a format for backbone - this ones for you Jase ;-)
        if ($grouped == true) {

            // ($tournament->tournament_sponsor_name) ? $tournamentName = $tournament->name .' - '.$tournament->tournament_sponsor_name : $tournamentName = $tournament->name;

            $tournamentDetails = array(
                'id' => (int)$tournament -> id,
                'name' => $tournament->name,
                'buy_in' => (int)$tournament -> buy_in,
                'entry_fee' => (int)$tournament -> entry_fee,
                'num_entries' => (int)$numRegistrations,
                'prize_pool' => $prizePool,
                'places_paid' => $places_paid,
                'start_currency' => (int)$tournament -> start_currency,
                'bet_limit_flag' => (int)$tournament -> bet_limit_flag,
                'featured' => $featuredTournamentFlag,
                'reinvest_winnings_flag' => $tournament->reinvest_winnings_flag,
                'closed_betting_on_first_match_flag' => $tournament->closed_betting_on_first_match_flag,
                'tournament_sponsor_name' => $tournament->tournament_sponsor_name,
                'start_date' => TimeHelper::isoDate($tournament -> start_date),
                'end_date' => TimeHelper::isoDate($tournament -> end_date),
                
                //rebuy info
                'rebuys' => $tournament->rebuys,
                'rebuy_currency' => $tournament->rebuy_currency,
                'rebuy_entry' => $tournament->rebuy_entry,
                'rebuy_buyin' => $tournament->rebuy_buyin,
                'rebuy_end' => $tournament->rebuy_end,
                
                //topup info
                'topups' => $tournament->topups,
                'topup_currency' => $tournament->topup_currency,
                'topup_entry' => $tournament->topup_entry,
                'topup_buyin' => $tournament->topup_buyin,
                'topup_end_date' => $tournament->topup_end_date,
                'topup_start_date' => $tournament->topup_start_date,
            );

            $tournament = \TopBetta\Models\TournamentModel::find($tournament->id);
            $tournamentParent = \TopBetta\Models\RaceMeeting::find($tournament->eventGroup->events->first()->competition->first()->id);

            //($tournament->tournament_sponsor_name) ? $tournamentName = $tournamentParent->name .' - '.$tournament->tournament_sponsor_name : $tournamentName = $tournamentParent->name;

            return array('success' => true, 'result' => array(
                'id' => (int)$tournamentParent -> id,
                'name' => $tournamentParent -> name,
                'state' => $tournamentParent -> state,
                'weather' => $tournamentParent -> weather,
                'track' => $tournamentParent -> track,
                'sub_type' => strtolower($tournamentParent -> type_code),
                'tournament_type' => ($isRacingTournament) ? 'r' : 's',
                'tournament' => $tournamentDetails
            ));

        }

        //our normal tournament info
        if ($isRacingTournament) {

            // ::: racing related data :::
            return array('success' => true, 'result' => array('parent_tournament_id' => (int)$tournament -> parent_tournament_id, 'meeting_id' => (int)$meetingId, 'name' => $tournament -> name, 'description' => $tournament -> description, 'start_currency' => (int)$tournament -> start_currency, 'start_date' => TimeHelper::isoDate($tournament -> start_date), 'end_date' => TimeHelper::isoDate($tournament -> end_date), 'end_date' => TimeHelper::isoDate($tournament -> end_date), 'jackpot_flag' => ($tournament -> jackpot_flag == 0) ? false : true, 'num_registrations' => (int)$numRegistrations, 'buy_in' => (int)$tournament -> buy_in, 'entry_fee' => (int)$tournament -> entry_fee, 'paid_flag' => ($tournament -> paid_flag == 0) ? false : true, 'cancelled_flag' => ($tournament -> cancelled_flag == 0) ? false : true, 'cancelled_reason' => $tournament -> cancelled_reason, 'place_list' => $placeList, 'prize_pool' => $prizePool, 'players' => $playerList, 'leaderboard' => $leaderboard, 'places_paid' => $places_paid, 'private' => ($tournament -> private_flag == 0) ? false : true, 'password_protected' => false, 'tournament_type' => 'r', 'reinvest_winnings_flag' => $tournament->reinvest_winnings_flag, 'closed_betting_on_first_match_flag' => $tournament->closed_betting_on_first_match_flag, 'tournament_sponsor_name' => $tournament->tournament_sponsor_name,
                //rebuy info
                'rebuys' => $tournament->rebuys,
                'rebuy_currency' => $tournament->rebuy_currency,
                'rebuy_entry' => $tournament->rebuy_entry,
                'rebuy_buyin' => $tournament->rebuy_buyin,
                'rebuy_end' => $tournament->rebuy_end,

                //topup info
                'topups' => $tournament->topups,
                'topup_currency' => $tournament->topup_currency,
                'topup_entry' => $tournament->topup_entry,
                'topup_buyin' => $tournament->topup_buyin,
                'topup_end_date' => $tournament->topup_end_date,
                'topup_start_date' => $tournament->topup_start_date,
            ));



        } else {

            // ::: sports related data :::
            return array('success' => true, 'result' => array('parent_tournament_id' => (int)$tournament -> parent_tournament_id, 'competition_id' => (int)$meetingId, 'events' => $events, 'name' => $tournament -> name, 'description' => $tournament -> description, 'start_currency' => (int)$tournament -> start_currency, 'start_date' => \TopBetta\Helpers\TimeHelper::isoDate($tournament -> start_date), 'end_date' => \TopBetta\Helpers\TimeHelper::isoDate($tournament -> end_date), 'jackpot_flag' => ($tournament -> jackpot_flag == 0) ? false : true, 'num_registrations' => (int)$numRegistrations, 'buy_in' => (int)$tournament -> buy_in, 'entry_fee' => (int)$tournament -> entry_fee, 'closed_betting_on_first_match_flag' => ($tournament -> closed_betting_on_first_match_flag == 0) ? false : true, 'reinvest_winnings_flag' => ($tournament -> reinvest_winnings_flag == 0) ? false : true, 'paid_flag' => ($tournament -> paid_flag == 0) ? false : true, 'cancelled_flag' => ($tournament -> cancelled_flag == 0) ? false : true, 'cancelled_reason' => $tournament -> cancelled_reason, 'place_list' => $placeList, 'prize_pool' => $prizePool, 'players' => $playerList, 'leaderboard' => $leaderboard, 'places_paid' => $places_paid, 'private' => ($tournament -> private_flag == 0) ? false : true, 'password_protected' => false, 'tournament_type' => 's', 'reinvest_winnings_flag' => $tournament->reinvest_winnings_flag, 'closed_betting_on_first_match_flag' => $tournament->closed_betting_on_first_match_flag, 'tournament_sponsor_name' => $tournament->tournament_sponsor_name,
                //rebuy info
                'rebuys' => $tournament->rebuys,
                'rebuy_currency' => $tournament->rebuy_currency,
                'rebuy_entry' => $tournament->rebuy_entry,
                'rebuy_buyin' => $tournament->rebuy_buyin,
                'rebuy_end' => $tournament->rebuy_end,

                //topup info
                'topups' => $tournament->topups,
                'topup_currency' => $tournament->topup_currency,
                'topup_entry' => $tournament->topup_entry,
                'topup_buyin' => $tournament->topup_buyin,
                'topup_end_date' => $tournament->topup_end_date,
                'topup_start_date' => $tournament->topup_start_date,
            ));

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //
    }

}
