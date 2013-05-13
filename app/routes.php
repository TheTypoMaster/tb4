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
	return View::make('hello');
});

// Route group for backend API.
//Route::group(array('prefix' => '/api/backend/v1', 'before' => 'apiauth'), function() {
Route::group(array('prefix' => '/api/backend/v1'), function() {
	// incoming race data and results
	Route::resource('racing', 'RacingController');
	// incoming sports data and results
	Route::resource('sports', 'SportsController');
	// incoming results for bet's placed
	Route::resource('betresults', 'BetResultsController');
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


});

Route::group(array('prefix' => 'api/backend/test'), function() {

	Route::resource('url', 'UrlController');

});