<?php namespace TopBetta\Providers;

use Auth;
use Route;
use Redirect;
use Config;
use Response;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'TopBetta\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function boot(Router $router)
	{
		// frontend api is using laravel auth after the legacy login
		Route::filter('auth', function()
		{
			if (Auth::guest()) return Response::json(array("success" => false, "error" => "Please login first."), 401);
		});

		Route::filter('auth.admin', function() {
			if (Auth::guest() || Auth::user()->gid != 25) return Redirect::guest('/admin/login');
		});

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
			Config::set('auth.model', 'TopBetta\Models\APIUserModel');

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
			Config::set('auth.model', 'TopBetta\Models\APIUserModel');
			return Auth::onceBasic('username');
		});

		
		parent::boot($router);
	}

	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function map(Router $router)
	{
		$router->group(['namespace' => $this->namespace], function($router)
		{
			require app_path('Http/routes.php');
		});
	}

}
