<?php


Route::get('/', 'HomeController@index');


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
    //incoming price data
    Route::resource('pricedata', 'Backend\RacingPriceController');
    //New sport feed endpoint
    Route::resource('sports-feed', 'Backend\SportsFeedController', array("only" => array('store')));
    //Trainer endpoint
    Route::resource('trainers', 'Backend\TrainerController', array("only" => array('store')));
    //Owner endpoint
    Route::resource('owners', 'Backend\OwnerController', array("only" => array('store')));
    //Runner endpoint
    Route::resource('runners', 'Backend\RunnerController', array("only" => array('store')));

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

    Route::put('risk-show-event/{event}', 'Backend\RiskEventsController@showEvent');
    Route::put('risk-hide-event/{event}', 'Backend\RiskEventsController@hideEvent');

    Route::put('risk-show-competition/{competition}', 'Backend\RiskCompetitionController@showCompetition');
    Route::put('risk-hide-competition/{competition}', 'Backend\RiskCompetitionController@hideCompetition');

    Route::post('meeting-products', 'Backend\ProductController@setMeetingProducts');
    Route::post('user-products', 'Backend\ProductController@setUserProducts');

    Route::post('override-price', 'Backend\PricesController@override');

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

    //user tournament bets
    Route::resource('user.tournament.bets', 'Frontend\UserTournamentBetsController');

    // ::: SPECIAL COMBINED CALLS :::
    Route::get('combined/tournaments', 'Frontend\FrontCombinedTournamentsController@index');
    Route::get('combined/racing', 'Frontend\FrontCombinedRacingController@index');
    Route::get('combined/racingNew', 'Frontend\FrontCombinedRacingController@indexNew');
    Route::get('combined/sports', 'Frontend\FrontCombinedSportsController@index');

    // Temporary feed routes for sports - another branch has a better implimentation
    Route::get('feed/sports.{ext}', 'Frontend\FeedController@index');
    //Route::get('feed/competitions', 'TopBetta\Controllers\FeedController@competitions');
    // Route::get('feed/sports', 'TopBetta\Controllers\FeedController@sports');

    // --- NEW DEPOSIT ROUTES ---
    Route::resource('deposits', 'Frontend\DepositsController');
    Route::resource('scheduled-deposits', 'Frontend\ScheduledDepositsController');
    Route::resource('eway-tokens', 'Frontend\EwayCreditCardController');



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
    Route::resource('bet-limits', 'Admin\BetLimitsController');
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
    Route::get('tournaments/get-markets/{competitionId}', 'Admin\TournamentsController@getMarkets');
    Route::get('tournaments/get-parent-tournaments/{sportId}', 'Admin\TournamentsController@getParentTournaments');
    Route::post('tournaments/add-users/{tournamentId}', 'Admin\TournamentsController@addUsers');
    Route::post('tournaments/remove/{tournamentId}/{userId}', 'Admin\TournamentsController@removeUserFromTournament');
    Route::get('tournaments/cancel/{tournamentId}', 'Admin\TournamentsController@cancelForm');
    Route::post('tournaments/cancel/{tournamentId}', 'Admin\TournamentsController@cancel');
    Route::get('tournaments/download/entrants', 'Admin\TournamentsController@downloadEntrants');
    Route::get('tournaments/get-event-groups-by-type/{typeid}', 'Admin\TournamentsController@getEventGroupsByType');

    //tournament event groups
    Route::get('event-groups', 'Admin\TournamentEventGroupController@index');
    Route::get('event-groups/create', 'Admin\TournamentEventGroupController@create');
    Route::post('event-groups/store', 'Admin\TournamentEventGroupController@store');
    Route::get('event-groups/edit/{id}', 'Admin\TournamentEventGroupController@edit');
    Route::get('event-groups/delete/{id}', 'Admin\TournamentEventGroupController@destroy');
    Route::get('get-event-groups/{id}', 'Admin\TournamentEventGroupController@getEvnetGruops');
    Route::get('get-events/{id}', 'Admin\TournamentEventGroupController@getEventsByEventGroup');
    Route::get('event-groups/keepadding/{group_name}/{group_id}', 'Admin\TournamentEventGroupController@keepAdding');
    Route::get('event-groups/remove_event/{group_id}/{event_id}/{group_name}', 'Admin\TournamentEventGroupController@removeEventFromGroup');
    Route::post('event-groups/update/{id}', 'Admin\TournamentEventGroupController@store');

    //tournament comments routes
    Route::get('tournament-comments', 'Admin\TournamentCommentsController@index');
    Route::get('tournament-comments/delete/{id}', 'Admin\TournamentCommentsController@destroy');
    Route::post('tournament-comments/store', 'Admin\TournamentCommentsController@store');
    Route::get('tournament-comments/block/{id}', 'Admin\TournamentCommentsController@update');


    //tournament groups
    Route::resource('tournament-groups', 'Admin\TournamentGroupController');

    // market type groups
    Route::get('market-groups', 'Admin\MarketTypeGroupController@index');
    Route::get('market-groups/create', 'Admin\MarketTypeGroupController@create');
    Route::post('market-groups/store', 'Admin\MarketTypeGroupController@store');
    Route::get('market-groups/edit/{id}', 'Admin\MarketTypeGroupController@edit');
    Route::post('market-groups/update/{id}', 'Admin\MarketTypeGroupController@update');
    Route::get('market-groups/delete/{id}', 'Admin\MarketTypeGroupController@destroy');

    //prize format
    Route::get('prize-format', 'Admin\PrizeFormatController@index');
    Route::get('prize-format/edit/{id}', 'Admin\PrizeFormatController@edit');
    Route::post('prize-format/update/{id}', 'Admin\PrizeFormatController@update');



    // tournament settings
    Route::get('tournament-settings', 'Admin\TournamentSettingsController@edit');
    Route::put('tournament-settings', 'Admin\TournamentSettingsController@update');

    // From Sports Branch
    Route::resource('marketordering', 'Admin\MarketOrderingController');
    Route::resource('basecompetitions', 'Admin\BaseCompetitionController');
    Route::resource('teams', 'Admin\TeamController');
    Route::resource('players', 'Admin\PlayerController');
    Route::resource('competitionregions', 'Admin\CompetitionRegionController');
    Route::resource('icons', 'Admin\IconController');

    //user activity
    Route::post('user-activity/download', 'Admin\UserActivityController@createUserActivity');
    Route::get('user-activity/download', 'Admin\UserActivityController@downloadUserActivity');
    Route::resource('user-activity', 'Admin\UserActivityController');

    Route::get('sports-list', 'Admin\SportsController@getSports');
    Route::get('sports/{sportId}/competitions', 'Admin\CompetitionsController@getBySport');

    //market type details
    Route::resource("market-type-details", 'Admin\SportMarketTypeDetailsController');

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

Route::group(array('prefix' => '/api/external/v1', 'after' => 'topbetta_secure_links', 'middleware' => 'destroy.session'), function() {
    // --- NEW DEPOSIT ROUTES ---
    Route::resource('deposits', 'Frontend\DepositsController');
    Route::resource('scheduled-deposits', 'Frontend\ScheduledDepositsController');
    Route::resource('eway-tokens', 'Frontend\EwayCreditCardController');
});

// new login/logout methods
Route::group(array('prefix' => '/api/v1', 'after' => 'topbetta_secure_links'), function() {

    // normal login
    Route::post('authentication/login', 'Frontend\UserSessionController@loginExternal');

    // normal logout
    Route::get('authentication/logout', 'Frontend\UserSessionController@logout');

    //Get auth user
    Route::get('authentication/user', 'Frontend\UserSessionController@user');

    // token login
    Route::get('authentication/token/login', 'Frontend\UserTokenController@tokenLogin');

});


/***********************************************************************************************************************
 * V2 ROUTES
 **********************************************************************************************************************/
Route::group(array('prefix' => '/api/v2', 'before' => 'not.excluded'), function(){

    // --- RACING ROUTES ---
    Route::resource('meetings', 'Frontend\MeetingsController', array("only" => array('index', 'show')));
    Route::get('combined/meetings/races', 'Frontend\MeetingsController@getMeetingsWithRaces');
    Route::get('combined/meeting/races', 'Frontend\MeetingRacesController@index');
    Route::get('combined/meeting/races/selections', 'Frontend\MeetingRaceSelectionsController@index');
    Route::get('combined/meetings/races/selections', 'Frontend\MeetingRaceSelectionsController@getMeetingsWithSelectionsForMeeting');
    Route::get('combined/race/selections', 'Frontend\RaceSelectionsController@index');
    Route::get('races/{id}', 'Frontend\RaceController@show');

    // --- N2J ---
    Route::get('/racing/next-to-jump', 'Frontend\FrontRacesController@nextToJump');
    Route::get('/racing/next-to-jump-eventids', 'Frontend\FrontRacesController@nextToJumpEventIds');
    Route::get('/racing/fast-bet', 'Frontend\FrontRacesController@fastBetEvents');

    // --- SPORTS ROUTES ---
    Route::get('visible-sports', 'Frontend\SportsController@getVisibleSports');
    Route::get('sport/competitions', 'Frontend\CompetitionsController@getCompetitionsForSport');
    Route::get('combined/sports/competitions', 'Frontend\SportsController@getVisibleSportsWithCompetitions');
    Route::get('combined/sports/competition/events', 'Frontend\SportsController@getVisibleSportsWithSelectedCompetition');
    Route::get('combined/events/markets/selections', 'Frontend\EventsController@getEventsForCompetition');
    Route::get('combined/markets/selections', 'Frontend\MarketsController@getAllMarketsForEvent');
    Route::get('/sports/competition/market-types', 'Frontend\MarketTypesController@getMarketTypesForCompetition');

    // ---SPORT REST ROUTES ---
    Route::resource('sports', 'Frontend\SportsController', array("only" => array('index', 'show')));
    Route::resource('competitions', 'Frontend\CompetitionsController', array("only" => array('index', 'show')));
    Route::resource('base-competitions', 'Frontend\BaseCompetitionController', array("only" => array('index', 'show')));
    Route::resource('events', 'Frontend\EventsController', array("only" => array('index', 'show')));
    Route::resource('markets', 'Frontend\MarketsController', array("only" => array('index', 'show')));
    Route::resource('market-types', 'Frontend\MarketTypesController', array("only" => array('index', 'show')));
    Route::resource('selections', 'Frontend\SelectionsController', array("only" => array('index', 'show')));
    Route::resource('teams', 'Frontend\TeamsController', array("only" => array('index', 'show')));
    Route::resource('players', 'Frontend\PlayersController', array("only" => array('index', 'show')));
    Route::resource('prices', 'Frontend\PricesController', array("only" => array('index', 'show')));
    Route::resource('results', 'Frontend\ResultsController', array("only" => array('index', 'show')));

    // --- SPORTS N2J ---
    Route::get('/sports/events/next-to-jump', 'Frontend\EventsController@nextToJump');

    // --- TOURNAMENT ROUTES ---
    Route::get('combined/tournament-groups/tournaments', 'Frontend\TournamentGroupController@getVisibleTournamentGroupsWithTournaments');
    Route::resource('tournaments', 'Frontend\TournamentController', array("only" => array('index', 'show')));
    Route::resource('tournament.leaderboard', 'Frontend\TournamentLeaderboardController', array("only" => array('index')));
    Route::get('combined/tournament/events', 'Frontend\TournamentController@getTournamentWithEvents');
    Route::get('comments', 'Frontend\TournamentCommentController@index');

    Route::group(array('before' => 'auth'), function() {
        Route::get('ticket', 'Frontend\TicketsController@getTicketForUserInTournament');
        Route::get('active-tickets', 'Frontend\TicketsController@getRecentAndActiveTicketsForUser');
        Route::get('tournaments/tickets/next-to-jump', 'Frontend\TicketsController@nextToJump');
        Route::resource('tournament-bets', 'Frontend\TournamentBetsController', array("only" => array('index', 'store')));
        Route::resource('tickets', 'Frontend\TicketsController', array("only" => array('index', 'store', 'show')));
        Route::post('comments', 'Frontend\TournamentCommentController@store');

        //tournament rebuys and topups
        Route::post('tournaments/tickets/{ticketId}/rebuy', 'Frontend\FrontTournamentsTicketsController@rebuy');
        Route::post('tournaments/tickets/{ticketId}/topup', 'Frontend\FrontTournamentsTicketsController@topup');
    });



    //user tournament bets
    Route::resource('user.tournament.bets', 'Frontend\UserTournamentBetsController', array("only" => array('index')));

    // --- BETS ----
    Route::group(array('before' => 'auth'), function() {
        Route::resource('bets', 'Frontend\BetController', array("only" => array('index', 'store')));
        Route::get('active-bets', 'Frontend\BetController@getActiveAndRecentBets');
        Route::resource('competition.bets', 'Frontend\CompetitionBetsController', array("only" => array('index')));
    });

    // --- USERS ---
    Route::group(array('before' => 'auth'), function() {
        Route::get('user/transactions', 'Frontend\AccountTransactionController@index');
        Route::post('user/withdrawal/{type}', 'Frontend\WithdrawalController@store');

        // -- BET LIMITS
        Route::post('user/set-bet-limit', 'Frontend\UserBetLimitController@setBetLimit');
        Route::post('user/remove-bet-limit', 'Frontend\UserBetLimitController@removeBetLimit');
        Route::get('user/bet-limit', 'Frontend\UserBetLimitController@getBetLimit');
    });
    //create account
    Route::post('registration/createfull', 'Frontend\UserRegistrationController@createFull');
    //activation
    Route::get('registration/activate/{activationHash}', 'Frontend\UserRegistrationController@activate');
    Route::get('registration/resend-welcome-email/{userId}', 'Frontend\UserRegistrationController@resendWelcomeEmail');

    // --- AFFILIATE ROUTES ---
    Route::resource('affiliate.acl', 'Frontend\ACLController', array('only' => array('show')));

    // --- PASSWORD RESETS ---
    // The actual reset password method
    Route::post('password_resets/reset', 'Frontend\FrontPasswordResetsController@postReset');
    //The email gets sent via this method
    Route::post('password_resets', 'Frontend\FrontPasswordResetsController@store');


    // Temporary feed routes for sports - another branch has a better implimentation
    Route::get('feed/sports.{ext}', 'Frontend\FeedController@index');
    //Route::get('feed/competitions', 'TopBetta\Controllers\FeedController@competitions');
    // Route::get('feed/sports', 'TopBetta\Controllers\FeedController@sports');

    // --- NEW DEPOSIT ROUTES ---
    Route::resource('deposits', 'Frontend\DepositsController', array("only" => array('store')));
    Route::resource('scheduled-deposits', 'Frontend\ScheduledDepositsController', array("only" => array('index', 'store', 'destroy')));
    Route::resource('eway-tokens', 'Frontend\EwayCreditCardController', array("only" => array('index', 'destroy')));
    Route::resource('poli-deposit', 'Frontend\FrontUsersPoliDepositController');


    //CONTACT USE ENDPOINT
    Route::post('contact-us', 'Frontend\ContactController@contactUs');
});

// new login/logout methods
Route::group(array('prefix' => '/api/v2', 'after' => 'topbetta_secure_links'), function() {

    // normal login
    Route::post('authentication/login', 'Frontend\UserSessionController@login');

    // normal logout
    Route::get('authentication/logout', 'Frontend\UserSessionController@logout');

    //Get auth user
    Route::get('authentication/user', 'Frontend\UserSessionController@user');

    // token login
    Route::get('authentication/token/login', 'Frontend\UserTokenController@tokenLogin');

});

Route::group(array('prefix' => '/api/external/v1', 'after' => 'topbetta_secure_links'), function() {

    Route::post('registration/tournament/create', 'External\UserAccountController@createTournamentAccount');
    Route::post('authentication/tournament/token/request', 'External\UserTokenController@requestToken');

});

Route::group(array('prefix' => '/api/external/v1', 'after' => 'topbetta_secure_links', 'middleware' => ['auth.token']), function() {
    Route::get('test', 'External\UserTokenController@test');

});

Route::group(array('prefix' => '/api/external/v1'), function() {
    Route::post('test-entry', 'External\TestController@testEntry');
});
