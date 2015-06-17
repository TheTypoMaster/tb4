<?php


Route::get('/', function() {
	return \Redirect::to('https://www.topbetta.com.au');
});

// Route group for backend API. Uses basic stateless auth filter
Route::group(array('prefix' => '/api/backend/v1', 'before' => 'basic.once'), function() { //, 'before' => 'basic.once'

	/*
	 * Data Feeds
	 */
	// incoming race data and results
	Route::resource('racing', 'Backend\RacingController');
	// incoming sports data and results
	Route::resource('sports', 'Backend\SportsController');
	// incoming results for bet's placed
	Route::resource('betresults', 'Backend\BetResultsController');
	//incoming race result data
	Route::resource('raceresults', 'Backend\RacingResultsController');
	//incoming race data
	Route::resource('racedata', 'Backend\RacingDataController');
    //New sport feed endpoint
    Route::resource('sports-feed', 'Backend\SportsFeedController', array("only" => array('store')));

    /*
     * Risk Manager
     */
	// special case where Risk Manager can push race results to TopBetta
	Route::resource('risk-results', 'Backend\RiskResultsController', array('only' => array('store')));
	// special case where Risk Manager can push race status changes to TopBetta
	Route::resource('risk-race-status', 'Backend\RiskRaceStatusController', array('only' => array('store')));
	// Risk can send query sport bets
	Route::resource('risk-sport-bets', 'Backend\RiskSportBetsController');
	// Risk can send query racing bets
	Route::resource('risk-bets', 'Backend\RiskBetsController');
	// special case where Risk Manager can push sport market results to TopBetta
	Route::resource('risk-result-sport-market', 'Backend\RiskResultSportMarketController');
	//Risk endpoint to get user account details
	Route::resource('risk-user-account', 'Backend\RiskUserAccountController', array('only' => array('show')));

	Route::put('risk-show-event/{event}', "Backend\RiskEventsController@showEvent");
	Route::put('risk-hide-event/{event}', "Backend\RiskEventsController@hideEvent");

	Route::put('risk-show-competition/{competition}', "Backend\RiskCompetitionController@showCompetition");
	Route::put('risk-hide-competition/{competition}', "Backend\RiskCompetitionController@hideCompetition");

});


// Route group for consumer API
Route::group(array('prefix' => '/api/v1', 'before' => 'not.excluded'), function() {

	// ::: USER :::
	Route::get('usersTournamentHistory', 'Frontend\FrontUsersTournamentsController@usersTournamentHistory');
	// 2 custom routes for users auth
	Route::post('users/login', 'Frontend\FrontUsersController@login');
	Route::get('users/logout', 'Frontend\FrontUsersController@logout');


	// custom route for refer a friend
	Route::resource('users/refer', 'Frontend\FrontUsersReferController');

	Route::resource('users', 'Frontend\FrontUsersController');
	Route::resource('users.profile', 'Frontend\FrontUsersProfileController');
	Route::resource('users.balances', 'Frontend\FrontUsersBalancesController');
	Route::resource('users.banking', 'Frontend\FrontUsersBankingController');
	Route::resource('users.deposit', 'Frontend\FrontUsersDepositController');
	Route::resource('users.withdraw', 'Frontend\FrontUsersWithdrawController');
	Route::resource('users.betting', 'Frontend\FrontUsersBettingController');
	Route::resource('users.betting-limit', 'Frontend\FrontUsersBettingLimitsController');
	Route::resource('users.tournaments', 'Frontend\FrontUsersTournamentsController');
	Route::resource('users.poli-deposit', 'Frontend\FrontUsersPoliDepositController');

	// Password Resets
	// The actual reset password method
	Route::post('password_resets/reset', 'Frontend\FrontPasswordResetsController@postReset');
	//The email gets sent via this method
	Route::post('password_resets', 'Frontend\FrontPasswordResetsController@store');

	// ::: BETS :::
	Route::resource('bets', 'Frontend\FrontBetsController');

	// ::: RACING :::
	//Racing Meetings
	Route::resource('racing/meetings', 'Frontend\FrontMeetingsController');
	Route::resource('racing/meetings.races', 'Frontend\FrontRacesController');
	Route::resource('racing/meetings.races.runners', 'Frontend\FrontRunnersController');

	//Racing Races
	Route::get('/racing/races/next-to-jump', 'Frontend\FrontRacesController@nextToJump');
	Route::get('/racing/races/next-to-jump-eventids', 'Frontend\FrontRacesController@nextToJumpEventIds');
	Route::get('/racing/fast-bet', 'Frontend\FrontRacesController@fastBetEvents');
	Route::resource('racing/races', 'Frontend\FrontRacesController');

	//Racing Runners
	Route::resource('racing/runners', 'Frontend\FrontRunnersController');

	// ::: SPORTS :::
	//Sports events
	Route::get('/sports/events/next-to-jump', 'Frontend\FrontSportsEventsController@nextToJump');
	Route::resource('sports/events', 'Frontend\FrontSportsEventsController');

	//Sports types
	Route::resource('sports/types', 'Frontend\FrontSportsTypesController');

	//Sports options
	Route::resource('sports/options', 'Frontend\FrontSportsOptionsController');

	//Sports results
	Route::resource('sports/results', 'Frontend\FrontSportsResultsController');

	//Sports and comps
	Route::resource('sports', 'Frontend\FrontSportsController');
	Route::resource('sports.events', 'Frontend\FrontSportsEventsController');
	Route::resource('sports.events.types', 'Frontend\FrontSportsTypesController');
	Route::resource('sports.events.types.options', 'Frontend\FrontSportsOptionsController');

	// ::: TOURNAMENTS :::
	//tournaments
	Route::resource('tournaments', 'Frontend\FrontTournamentsController');

	//tournaments bets
	Route::resource('tournaments.comments', 'Frontend\FrontTournamentsCommentsController');

	//tournaments bets
	Route::resource('tournaments-bets', 'Frontend\FrontTournamentsBetsController');

	//tournaments details
	Route::resource('tournaments.details', 'Frontend\FrontTournamentsDetailsController');

	//tournaments tickets
	Route::get('/tournaments/tickets/next-to-jump', 'Frontend\FrontTournamentsTicketsController@nextToJump');
	Route::resource('tournaments.tickets', 'Frontend\FrontTournamentsTicketsController');

	//tournament rebuys and topups
	Route::post('tournaments/tickets/{ticketId}/rebuy', 'Frontend\FrontTournamentsTicketsController@rebuy');
	Route::post('tournaments/tickets/{ticketId}/topup', 'Frontend\FrontTournamentsTicketsController@topup');

	// ::: SPECIAL COMBINED CALLS :::
	Route::get('combined/tournaments', 'Frontend\FrontCombinedTournamentsController@index');
	Route::get('combined/racing', 'Frontend\FrontCombinedRacingController@index');
	Route::get('combined/racingNew', 'Frontend\FrontCombinedRacingController@indexNew');
	Route::get('combined/sports', 'Frontend\FrontCombinedSportsController@index');

	// Temporary feed routes for sports - another branch has a better implimentation
	Route::get('feed/sports.{ext}', 'Frontend\FeedController@index');
	//Route::get('feed/competitions', 'TopBetta\Controllers\FeedController@competitions');
	// Route::get('feed/sports', 'TopBetta\Controllers\FeedController@sports');



});


/*
 * Admin Login & Session Handling
 */
Route::group(array('prefix' => 'admin', 'after' => 'topbetta_secure_links'), function() {
	Route::get('/', array('as' => 'home', 'uses' => 'Admin\SessionController@create'));

	Route::get('login', 'Admin\SessionController@create');
	Route::get('logout', 'Admin\SessionController@destroy');

	Route::resource('session', 'Admin\SessionController', array('only' => array('create', 'store', 'destroy')));
});

/*
 * Admin Routes
 */
Route::group(array('prefix' => 'admin', 'before' => 'auth.admin', 'after' => 'topbetta_secure_links'), function() {

	Route::resource('account-transactions', 'Admin\AccountTransactionsController');
	Route::resource('bet-limits', 'Admin\BetlimitsController');
	Route::resource('bets', 'Admin\BetsController');
	Route::resource('competitions', 'Admin\CompetitionsController');
	Route::resource('dashboard', 'Admin\DashboardController');
	Route::resource('events', 'Admin\EventsController');
	Route::resource('free-credit-management', 'Admin\FreeCreditManagementController');
	Route::resource('free-credit-transactions', 'Admin\FreeCreditTransactionsController');
    Route::resource('groups', 'Admin\GroupsController');
	Route::resource('markets', 'Admin\MarketsController');
	Route::resource('markettypes', 'Admin\MarketTypeController');
	Route::resource('promotions', 'Admin\PromotionController');
	Route::resource('reports', 'Admin\ReportsController', array('only' => array('index', 'show')));
	Route::resource('selectionprices', 'Admin\SelectionPricesController');
	Route::resource('selections', 'Admin\SelectionsController');
	Route::resource('settings', 'Admin\SettingsController');
	Route::resource('sports', 'Admin\SportsController');
	Route::resource('tournaments', 'Admin\TournamentsController');
    Route::resource('tournament-sport-results', 'Admin\TournamentSportResultsController');
    Route::resource('tournament-sport-markets', 'Admin\EventGroupMarketsController');
    Route::resource('users', 'Admin\UsersController');
    Route::resource('user-permissions', 'Admin\UserPermissionsController');
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

	// From Sports Branch
	Route::resource('marketordering', 'Admin\MarketOrderingController');
	Route::resource('basecompetitions', 'Admin\BaseCompetitionController');
	Route::resource('teams', 'Admin\TeamController');
	Route::resource('players', 'Admin\PlayerController');
	Route::resource('competitionregions', 'Admin\CompetitionRegionController');
	Route::resource('icons', 'Admin\IconController');

	//user activity
	Route::post('user-activity/download', 'Admin\UserActivityController@downloadUserActivity');
	Route::resource('user-activity', 'Admin\UserActivityController');



});


// used for Punters Club application related things atm (Token Creation / Logins / Child Betting account creation and funding)
Route::group(array('prefix' => '/api/external/v1', 'before' => 'basic.once', 'after' => 'topbetta_secure_links'), function() {

	// Token request and login
	Route::post('authentication/token/request', 'Frontend\UserTokenController@tokenRequest');

	// Funds management/transfer
	Route::post('accounting/transfer', 'Frontend\FrontAccountingController@transferFunds');
    Route::post('accounting/returnfunds', 'Frontend\FrontAccountingController@returnFunds');

	// Full user account registration routes
	Route::post('registration/createfull', 'Frontend\UserRegistrationController@createFull');
	Route::post('registration/createclone', 'Frontend\UserRegistrationController@createFullChildFromClone');

	//create basic user
	Route::post("registration/createbasic", 'Frontend\UserRegistrationController@createBasic');

	//activation routes
	Route::get('registration/activate/{activationHash}', 'Frontend\UserRegistrationController@activate');

	Route::get('registration/resend-welcome-email/{userId}', 'Frontend\UserRegistrationController@resendWelcomeEmail');

});

// new login/logout methods
Route::group(array('prefix' => '/api/v1', 'after' => 'topbetta_secure_links'), function() {

	// normal login
	Route::post('authentication/login', 'Frontend\UserSessionController@login');

	// normal logout
	Route::get('authentication/logout', 'Frontend\UserSessionController@logout');

	// token login
	Route::get('authentication/token/login', 'Frontend\UserTokenController@tokenLogin');

});
