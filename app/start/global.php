<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a rotating log file setup which creates a new file each day.
|
*/

$logFile = 'laravel.log';

Log::useDailyFiles(storage_path().'/logs/'.$logFile);

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
	Log::error($exception);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenace mode is in effect for this application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';

/*
|--------------------------------------------------------------------------
| Admin views
|--------------------------------------------------------------------------
*/

View::addLocation(app('path').'/topbetta/admin/views');
View::addNamespace('admin', app('path').'/topbetta/admin/views');

/**
 * Add the missing helper to L4.0 :)
 * Create a select month field.
 *
 * @param  string  $name
 * @param  string  $selected
 * @param  array   $options
 * @return string
 */
Form::macro('selectMonth', function ($name, $selected = null, $options = array())
{
	$months = array();

	foreach (range(1, 12) as $month)
	{
		$months[$month] = strftime('%B', mktime(0, 0, 0, $month, 1));
	}

	return Form::select($name, $months, $selected, $options);
});

/**
 * Added a year select to match ;)
 * Create a select year field.
 *
 * @param  string  $name
 * @param  string  $selected
 * @param  array   $options
 * @return string
 */
Form::macro('selectYear', function ($name, $selected = null, $options = array())
{
	$years = array();

	foreach (range(date('Y')-4, date('Y')) as $year)
	{
		$years[$year] = $year;
	}

	return Form::select($name, $years, $selected, $options);
});