<?php


Route::get('/', function() {
	return \Redirect::to('https://www.topbetta.com.au');
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
	//incoming race data
	Route::resource('racedata', 'RacingData');
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
	//Risk endpoint to get user account details
	Route::resource('risk-user-account', 'RiskUserAccount', array('only' => array('show')));

	Route::put('risk-show-event/{event}', "RiskEvents@showEvent");
	Route::put('risk-hide-event/{event}', "RiskEvents@hideEvent");

	Route::put('risk-show-competition/{competition}', "RiskCompetition@showCompetition");
	Route::put('risk-hide-competition/{competition}', "RiskCompetition@hideCompetition");

});


// Route group for consumer API
Route::group(array('prefix' => '/api/v1', 'before' => 'not.excluded'), function() {

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
	Route::resource('users.poli-deposit', 'FrontUsersPoliDeposit');

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

	//tournament rebuys and topups
	Route::post('tournaments/tickets/{ticketId}/rebuy', 'FrontTournamentsTickets@rebuy');
	Route::post('tournaments/tickets/{ticketId}/topup', 'FrontTournamentsTickets@topup');

	// ::: SPECIAL COMBINED CALLS :::
	Route::get('combined/tournaments', 'FrontCombinedTournaments@index');
	Route::get('combined/racing', 'FrontCombinedRacing@index');
	Route::get('combined/racingNew', 'FrontCombinedRacing@indexNew');
	Route::get('combined/sports', 'FrontCombinedSports@index');

	// Temporary feed routes for sports - another branch has a better implimentation
	Route::get('feed/sports.{ext}', 'TopBetta\Controllers\FeedController@index');
	//Route::get('feed/competitions', 'TopBetta\Controllers\FeedController@competitions');
	// Route::get('feed/sports', 'TopBetta\Controllers\FeedController@sports');



});

Route::group(array('prefix' => 'admin', 'after' => 'topbetta_secure_links'), function() {
	Route::get('/', array('as' => 'home', 'uses' => 'Admin\SessionController@create'));

	Route::get('login', 'Admin\SessionController@create');
	Route::get('logout', 'Admin\SessionController@destroy');

	Route::resource('session', 'Admin\SessionController', array('only' => array('create', 'store', 'destroy')));
});

Route::group(array('prefix' => 'admin', 'before' => 'auth.admin', 'after' => 'topbetta_secure_links'), function() {

	Route::resource('account-transactions', 'Admin\AccountTransactionsController');
	Route::resource('bet-limits', 'Admin\BetlimitsController');
	Route::resource('bets', 'Admin\BetsController');
	Route::resource('competitions', 'Admin\CompetitionsController');
	Route::resource('dashboard', 'Admin\DashboardController');
	Route::resource('events', 'Admin\EventsController');
	Route::resource('free-credit-management', 'Admin\FreeCreditManagementController');
	Route::resource('free-credit-transactions', 'Admin\FreeCreditTransactionsController');
	Route::resource('markets', 'Admin\MarketsController');
	Route::resource('markettypes', 'Admin\MarketTypeController');
	Route::resource('promotions', 'Admin\PromotionController');
	Route::resource('reports', 'Admin\ReportsController', array('only' => array('index', 'show')));
	Route::resource('selectionprices', 'Admin\SelectionPricesController');
	Route::resource('selections', 'Admin\SelectionsController');
	Route::resource('settings', 'Admin\SettingsController');
	Route::resource('sports', 'Admin\SportsController');
	Route::resource('tournaments', 'Admin\TournamentsController');
	Route::resource('users', 'Admin\UsersController');
	Route::resource('users.account-transactions', 'Admin\UserAccountTransactionsController');
	Route::resource('users.bet-limits', 'Admin\UserBetLimitsController');
	Route::resource('users.bets', 'Admin\UserBetsController', array('only' => array('index')));
	Route::resource('users.deposit-limit', 'Admin\UserDepositLimitsController');
	Route::resource('users.free-credit-transactions', 'Admin\UserFreeCreditTransactionsController');
	Route::resource('users.tournaments', 'Admin\UserTournamentsController');
	Route::resource('users.withdrawals', 'Admin\UserWithdrawalsController');
	Route::resource('withdrawal-config', 'Admin\WithdrawalConfigController');
	Route::resource('withdrawals', 'Admin\WithdrawalsController');

	// custom tournament routes
	Route::get('removeFreeCredits', 'Admin\FreeCreditManagementController@removeDormantCredits');
	Route::get('tournaments/add-users/{tournamentId}', 'Admin\TournamentsController@addUsersForm');
	Route::get('tournaments/get-competitions/{sportId}', 'Admin\TournamentsController@getCompetitions');
	Route::get('tournaments/get-event-groups/{competitionId}', 'Admin\TournamentsController@getEventGroups');
	Route::get('tournaments/get-events/{eventGroupId}', 'Admin\TournamentsController@getEvents');
	Route::get('tournaments/get-parent-tournaments/{sportId}', 'Admin\TournamentsController@getParentTournaments');
	Route::post('tournaments/add-users/{tournamentId}', 'Admin\TournamentsController@addUsers');

});


// used for Punters Club application related things atm (Token Creation / Logins / Child Betting account creation and funding)
Route::group(array('prefix' => '/api/v1', 'before' => 'basic.once', 'after' => 'topbetta_secure_links'), function() {

	// Token request and login
	Route::post('authentication/token/request', 'Frontend\Controllers\UserTokenController@tokenRequest');

	// Funds management/transfer
	Route::post('accounting/transfer', 'Frontend\FrontAccountingController@transferFunds');

	// Full user account registration routes
	Route::post('registration/createfull', 'Frontend\Controllers\UserRegistrationController@createFull');
	Route::post('registration/createclone', 'Frontend\Controllers\UserRegistrationController@createFullChildFromClone');

	//create basic user
	Route::post("registration/createbasic", 'Frontend\Controllers\UserRegistrationController@createBasic');

	//activation routes
	Route::get('registration/activate/{activationHash}', 'Frontend\Controllers\UserRegistrationController@activate');

	Route::get('registration/resend-welcome-email/{userId}', 'Frontend\Controllers\UserRegistrationController@resendWelcomeEmail');

});

// new login/logout methods
Route::group(array('prefix' => '/api/v1', 'after' => 'topbetta_secure_links'), function() {

	// normal login
	Route::post('authentication/login', 'Frontend\Controllers\UserSessionController@login');

	// normal logout
	Route::get('authentication/logout', 'Frontend\Controllers\UserSessionController@logout');

	// token login
	Route::get('authentication/token/login', 'Frontend\Controllers\UserTokenController@tokenLogin');

});
