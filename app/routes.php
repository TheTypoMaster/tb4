<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */

// apc_clear_cache("user");
//TODO: ****** this is not safe to be here for production - find a better fix ******

if (function_exists('apache_request_headers')) {
	$requestHeaders = apache_request_headers();
} else {
	$requestHeaders = array();
}
if (array_key_exists('Origin', $requestHeaders)) {

	$httpOrigin = $requestHeaders['Origin'];
	$allowedHttpOrigins = array(
		"http://localhost:9778",
		"http://beta.mugbookie.com",
		"http://localhost",
		"http://beta.tb4.dev",
		"http://tb4test.mugbookie.com",
		"http://192.168.0.31:9778",
		"https://www.topbetta.com.au",
		"http://jason.mugbookie.com",
        "http://jasontb.mugbookie.com",
		"http://evan.mugbookie.com",
		"http://mic.mugbookie.com",
		"http://greg.mugbookie.com"
	);

	if (in_array($httpOrigin, $allowedHttpOrigins)) {

		@header("Access-Control-Allow-Origin: " . $httpOrigin);
	}
} else {

	header('Access-Control-Allow-Origin: http://localhost:9778');
}

header('Access-Control-Allow-Credentials: true');




Route::get('/', function() {

	return \Redirect::to('https://www.topbetta.com.au');

	//return  TopBetta\RisaForm::with('lastStarts')->where('runner_code', $runnerCode)->get();
	// return all events for meeting with id of 1
	//$events = RaceMeeting::find(1)->raceevents;
	//return $events;
	// return the meeting for event with id of 1
	//$meetings = RaceEvent::find(1)->racemeetings;
	//return $meetings;
	//$api = WageringApi::getInstance(WageringApi::API_IGAS);
	//$api_con=$api->checkConnection();
	//return (string)$api_con;
	//$it = Hash::make('igast3st1ng');
	//return $it;
	//return FreeTransactions::all();
	// return FreeCreditBalance::getFreeCreditBalance(6996);
});

//Route group for admin stuff
Route::group(array('prefix' => '/api/admin/v1', 'before' => 'basic.once'), function() {
	// Data importer calls
	Route::resource('dataimporter', 'AdminDataImporter');
	Route::resource('heartbeat', 'HeartBeat');

	// RISA form importer
	Route::get('risaformimporter', 'TopBetta\admin\RisaFormImporter@formImporter');
});

// Route group for backend API. Uses basic stateless auth filter
Route::group(array('prefix' => '/api/backend/v1', 'before' => 'basic.once'), function() { //, 'before' => 'basic.once'
	// incoming race data and results
	Route::resource('racing', 'BackRacing');
	// incoming sports data and results
	Route::resource('sports', 'BackSports');
	// incoming results for bet's placed
	Route::resource('betresults', 'BackBets');
    //incoming race result data
    Route::resource('raceresults', 'RaceResulting');
	// special case where Risk Manager can push race results to TopBetta
	Route::resource('risk-results', 'RiskResults', array('only' => array('store')));
	// special case where Risk Manager can push race status changes to TopBetta
	Route::resource('risk-race-status', 'RiskRaceStatus', array('only' => array('store')));
    // Risk can send query sport bets
    Route::resource('risk-sport-bets', 'RiskSportBets');
    // Risk can send query racing bets
    Route::resource('risk-bets', 'RiskBets');
    // special case where Risk Manager can push sport market results to TopBetta
    Route::resource('risk-result-sport-market', 'RiskResultSportMarket');
	// test JSON API
	Route::resource('testjson', 'testJSON');
});


// Route group for consumer API
Route::group(array('prefix' => '/api/v1'), function() {

	// ::: USER :::
	Route::get('usersTournamentHistory', 'FrontUsersTournaments@usersTournamentHistory');
	// 2 custom routes for users auth
	Route::post('users/login', 'FrontUsers@login');
	Route::get('users/logout', 'FrontUsers@logout');

	// custom route for refer a friend
	Route::resource('users/refer', 'FrontUsersRefer');

	Route::resource('users', 'FrontUsers');
	Route::resource('users.profile', 'FrontUsersProfile');
	Route::resource('users.balances', 'FrontUsersBalances');
	Route::resource('users.banking', 'FrontUsersBanking');
	Route::resource('users.deposit', 'FrontUsersDeposit');
	Route::resource('users.withdraw', 'FrontUsersWithdraw');
	Route::resource('users.betting', 'FrontUsersBetting');
	Route::resource('users.betting-limit', 'FrontUsersBettingLimit');
	Route::resource('users.tournaments', 'FrontUsersTournaments');

	// Password Resets
	// The actual reset password method
	Route::post('password_resets/reset', 'FrontPasswordResetsController@postReset');
	//The email gets sent via this method
	Route::post('password_resets', 'FrontPasswordResetsController@store');

	// ::: BETS :::
	Route::resource('bets', 'FrontBets');

	// ::: RACING :::
	//Racing Meetings
	Route::resource('racing/meetings', 'FrontMeetings');
	Route::resource('racing/meetings.races', 'FrontRaces');
	Route::resource('racing/meetings.races.runners', 'FrontRunners');

	//Racing Races
	Route::get('/racing/races/next-to-jump', 'FrontRaces@nextToJump');
	Route::get('/racing/races/next-to-jump-eventids', 'FrontRaces@nextToJumpEventIds');
	Route::get('/racing/fast-bet', 'FrontRaces@fastBetEvents');
	Route::resource('racing/races', 'FrontRaces');

	//Racing Runners
	Route::resource('racing/runners', 'FrontRunners');

	// ::: SPORTS :::
	//Sports events
	Route::get('/sports/events/next-to-jump', 'FrontSportsEvents@nextToJump');
	Route::resource('sports/events', 'FrontSportsEvents');

	//Sports types
	Route::resource('sports/types', 'FrontSportsTypes');

	//Sports options
	Route::resource('sports/options', 'FrontSportsOptions');

	//Sports results
	Route::resource('sports/results', 'FrontSportsResults');

	//Sports and comps
	Route::resource('sports', 'FrontSports');
	Route::resource('sports.events', 'FrontSportsEvents');
	Route::resource('sports.events.types', 'FrontSportsTypes');
	Route::resource('sports.events.types.options', 'FrontSportsOptions');

	// ::: TOURNAMENTS :::
	//tournaments
	Route::resource('tournaments', 'FrontTournaments');

	//tournaments bets
	Route::resource('tournaments.comments', 'FrontTournamentsComments');

	//tournaments bets
	Route::resource('tournaments-bets', 'FrontTournamentsBets');

	//tournaments details
	Route::resource('tournaments.details', 'FrontTournamentsDetails');

	//tournaments tickets
	Route::get('/tournaments/tickets/next-to-jump', 'FrontTournamentsTickets@nextToJump');
	Route::resource('tournaments.tickets', 'FrontTournamentsTickets');

	// ::: SPECIAL COMBINED CALLS :::
	Route::get('combined/tournaments', 'FrontCombinedTournaments@index');
	Route::get('combined/racing', 'FrontCombinedRacing@index');
	Route::get('combined/racingNew', 'FrontCombinedRacing@indexNew');
	Route::get('combined/sports', 'FrontCombinedSports@index');
});

Route::group(array('prefix' => 'admin', 'after' => 'topbetta_secure_links'), function() {
	Route::get('/', array('as' => 'home', 'uses' => 'TopBetta\admin\controllers\SessionController@create'));

	Route::get('login', 'TopBetta\admin\controllers\SessionController@create');
	Route::get('logout', 'TopBetta\admin\controllers\SessionController@destroy');

	Route::resource('session', 'TopBetta\admin\controllers\SessionController', array('only' => array('create', 'store', 'destroy')));
});

Route::group(array('prefix' => 'admin', 'before' => 'auth.admin', 'after' => 'topbetta_secure_links'), function() {
	
	Route::resource('dashboard', 'TopBetta\admin\controllers\DashboardController');
	Route::resource('users', 'TopBetta\admin\controllers\UsersController');
	Route::resource('users.bet-limits', 'TopBetta\admin\controllers\UserBetLimitsController');
	Route::resource('users.bets', 'TopBetta\admin\controllers\UserBetsController', array('only' => array('index')));
	Route::resource('users.tournaments', 'TopBetta\admin\controllers\UserTournamentsController');
	Route::resource('users.account-transactions', 'TopBetta\admin\controllers\UserAccountTransactionsController');
	Route::resource('users.free-credit-transactions', 'TopBetta\admin\controllers\UserFreeCreditTransactionsController');
	Route::resource('users.withdrawals', 'TopBetta\admin\controllers\UserWithdrawalsController');
	Route::resource('bet-limits', 'TopBetta\admin\controllers\BetlimitsController');
	Route::resource('bets', 'TopBetta\admin\controllers\BetsController');
	Route::resource('withdrawals', 'TopBetta\admin\controllers\WithdrawalsController');
	Route::resource('account-transactions', 'TopBetta\admin\controllers\AccountTransactionsController');
	Route::resource('free-credit-transactions', 'TopBetta\admin\controllers\FreeCreditTransactionsController');
	Route::resource('tournaments', 'TopBetta\admin\controllers\TournamentsController');
	Route::resource('reports', 'TopBetta\admin\controllers\ReportsController', array('only' => array('index', 'show')));
	Route::resource('settings', 'TopBetta\admin\controllers\SettingsController');
	Route::resource('sports', 'TopBetta\admin\controllers\SportsController');
	Route::resource('competitions', 'TopBetta\admin\controllers\CompetitionsController');
	Route::resource('markets', 'TopBetta\admin\controllers\MarketsController');
	Route::resource('events', 'TopBetta\admin\controllers\EventsController');
	Route::resource('selections', 'TopBetta\admin\controllers\SelectionsController');
	Route::resource('selectionprices', 'TopBetta\admin\controllers\SelectionPricesController');
});

Route::group(array('prefix' => 'api/backend/test'), function() {

	Route::resource('url', 'UrlController');
});


Route::group(array('after' => 'topbetta_secure_links'), function() {
    Route::get('login', array('as' => 'login', 'uses' => 'TopBetta\frontend\FrontUsersController@tempLogin'));
    Route::post('/login', array('as' => 'login', 'uses' => 'TopBetta\frontend\FrontUsersController@handleLogin'));
    Route::get('/profile', array('as' => 'profile', 'uses' => 'TopBetta\frontend\FrontUsersController@handleProfile'));
    Route::get('/logout', array('as' => 'logout', 'uses' => 'TopBetta\frontend\FrontUsersController@handleLogout'));
});

// used for token related things
Route::group(array('prefix' => '/api/v1', 'after' => 'topbetta_secure_links'), function() {
	Route::post('token/request', 'TopBetta\Frontend\FrontTokenController@tokenRequest');
});



