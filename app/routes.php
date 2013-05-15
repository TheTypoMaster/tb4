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
	
	
	
	return View::make('hello');
	
	//return FreeTransactions::all();
	
});

// Route group for backend API.
//Route::group(array('prefix' => '/api/backend/v1', 'before' => 'apiauth'), function() {
Route::group(array('prefix' => '/api/backend/v1'), function() {
	// incoming race data and results
	Route::resource('racing', 'TopBetta\backend\RacingController');
	// incoming sports data and results
	Route::resource('sports', 'TopBetta\backend\SportsController');
	// incoming results for bet's placed
	Route::resource('betresults', 'TopBetta\backend\BetResultsController');
});

// Route group for consumer API
Route::group(array('prefix' => '/api/v1'), function() {
				
	//Racing Meetings
	Route::resource('racing/meetings','FrontMeetings');
	Route::resource('racing/meetings.races','FrontRaces');
	Route::resource('racing/meetings.races.runners','FrontRunners');
	
	//Racing Races
	Route::get('/racing/races/next-to-jump', 'FrontRaces@nextToJump');
	Route::resource('racing/races','FrontRaces');
	
	//Racing Runners
	Route::resource('racing/runners','FrontRunners');


});

Route::group(array('prefix' => 'api/backend/test'), function() {

	Route::resource('url', 'UrlController');

});
