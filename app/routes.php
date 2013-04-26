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
Route::group(array('prefix' => '/api/v1', 'before' => 'apiauth'), function() {
	// incoming bet placements from website
	Route::resource('betting', 'BettingController');
});

	Route::group(array('prefix' => 'api/backend/test'), function() {
	
		Route::resource('url', 'UrlController');
	
	});