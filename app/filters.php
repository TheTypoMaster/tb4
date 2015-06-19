<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	// If user account is blocked then log them out
	//(Auth::user()->block == 1) ?: Auth::logout();

	//dd(Auth::user()->block);
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

// frontend api is using laravel auth after the legacy login
Route::filter('auth', function()
{
	if (Auth::guest()) return Response::json(array("success" => false, "error" => "Please login first."), 401);
});

//Route::filter('auth.admin', function($route) {
//	if (Auth::guest() || Auth::user()->gid != 25) return Redirect::guest('/admin/login');
//});

Route::filter('auth.admin', 'TopBetta\admin\Filters\AdminFilter');

Route::filter('not.excluded', function() {
	if(Auth::check()){
		if (Auth::user()->block == 1){
			Auth::logout();
			//return Response::json(array("success" => false, "error" => "Your Account is blocked"), 401);
		}
	}


});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

Route::filter('apiauth', function()
{
 
	Config::set('auth.table', 'tb_api_users');
	Config::set('auth.model', 'APIUser');
	
    // Test against the presence of Basic Auth credentials
    $creds = array(
        'username' => Request::getUser(),
        'password' => Request::getPassword(),
    );
 
    if ( ! Auth::attempt($creds) ) {
 
        return Response::json(array(
            'error' => true,
            'message' => 'Unauthorized Request'),
            401
        );
 
    }
 
});

// stateless HTTP Basic login for API
Route::filter('basic.once', function()
{
	Config::set('auth.table', 'tb_api_users');
	Config::set('auth.model', 'APIUser');
	return Auth::onceBasic('username');
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter('topbetta_secure_links', function($route, $request, $response)
{
	if (\Config::get('topbetta.secure_links')) {
		if($response instanceof \Illuminate\Http\Response)
		{
			$output = $response->getContent();
			$host = \Illuminate\Support\Facades\URL::getRequest()->getHost();
			
			// swap out the http links to https for only our admin links
			$output = str_replace("http://{$host}", "https://{$host}", $output);
			
			$response->setContent($output);
		}
	}
});
