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
	$requestHeaders        = apache_request_headers();
} else {
	$requestHeaders = array();
}
if ( array_key_exists('Origin', $requestHeaders) ) {

	$httpOrigin            = $requestHeaders['Origin'];
	$allowedHttpOrigins   = array(
                            "http://localhost:9778",
                            "http://beta.mugbookie.com",
                            "http://localhost",
                            "http://beta.tb4.dev",
							"http://tb4test.mugbookie.com",
							"http://192.168.0.31:9778",
							"https://www.topbetta.com.au"
                          );

	if (in_array($httpOrigin, $allowedHttpOrigins)){

		@header("Access-Control-Allow-Origin: " . $httpOrigin);

	}

} else {

	header('Access-Control-Allow-Origin: http://localhost:9778');

}

header('Access-Control-Allow-Credentials: true');




Route::get('/', function()
{
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
	return View::make('hello');

	//return FreeTransactions::all();

	// return FreeCreditBalance::getFreeCreditBalance(6996);

});

//Route group for admin stuff
Route::group(array('prefix' => '/api/admin/v1'), function() {
	// Data importer calls
	Route::resource('dataimporter', 'AdminDataImporter');
	Route::resource('heartbeat', 'HeartBeat');

});


// Route group for backend API. Uses basic stateless auth filter
Route::group(array('prefix' => '/api/backend/v1', 'before' => 'basic.once'), function() { //, 'before' => 'basic.once'
	// incoming race data and results
	Route::resource('racing', 'BackRacing');
	// incoming sports data and results
	Route::resource('sports', 'BackSports');
	// incoming results for bet's placed
	Route::resource('betresults', 'BackBets');
	// test JSON API
	Route::resource('testjson', 'testJSON');
});

// Route group for consumer API
Route::group(array('prefix' => '/api/v1'), function() {

	// ::: USER :::

	// 2 custom routes for users auth
	Route::post('users/login', 'FrontUsers@login');
	Route::get('users/logout', 'FrontUsers@logout');

	// custom route for refer a friend
	Route::resource('users/refer','FrontUsersRefer');

	Route::resource('users','FrontUsers');
	Route::resource('users.profile', 'FrontUsersProfile');
	Route::resource('users.balances','FrontUsersBalances');
	Route::resource('users.banking','FrontUsersBanking');
	Route::resource('users.deposit','FrontUsersDeposit');
	Route::resource('users.withdraw','FrontUsersWithdraw');
	Route::resource('users.betting', 'FrontUsersBetting');
	Route::resource('users.betting-limit', 'FrontUsersBettingLimit');
	Route::resource('users.tournaments', 'FrontUsersTournaments');

	// ::: BETS :::
	Route::resource('bets','FrontBets');

	// ::: RACING :::

	//Racing Meetings
	Route::resource('racing/meetings','FrontMeetings');
	Route::resource('racing/meetings.races','FrontRaces');
	Route::resource('racing/meetings.races.runners','FrontRunners');

	//Racing Races
	Route::get('/racing/races/next-to-jump', 'FrontRaces@nextToJump');
	Route::resource('racing/races','FrontRaces');

	//Racing Runners
	Route::resource('racing/runners','FrontRunners');

	// ::: SPORTS :::

	//Sports events
	Route::get('/sports/events/next-to-jump', 'FrontSportsEvents@nextToJump');
	Route::resource('sports/events','FrontSportsEvents');

	//Sports types
	Route::resource('sports/types','FrontSportsTypes');

	//Sports options
	Route::resource('sports/options','FrontSportsOptions');

	//Sports results
	Route::resource('sports/results','FrontSportsResults');

	//Sports and comps
	Route::resource('sports','FrontSports');
	Route::resource('sports.events','FrontSportsEvents');
	Route::resource('sports.events.types','FrontSportsTypes');
	Route::resource('sports.events.types.options','FrontSportsOptions');

	// ::: TOURNAMENTS :::

	//tournaments
	Route::resource('tournaments','FrontTournaments');

	//tournaments bets
	Route::resource('tournaments-bets','FrontTournamentsBets');

	//tournaments details
	Route::resource('tournaments.details','FrontTournamentsDetails');

	//tournaments tickets
	Route::get('/tournaments/tickets/next-to-jump', 'FrontTournamentsTickets@nextToJump');
	Route::resource('tournaments.tickets','FrontTournamentsTickets');

});

Route::group(array('prefix' => 'api/backend/test'), function() {

	Route::resource('url', 'UrlController');

});
