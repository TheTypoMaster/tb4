<?php namespace TopBetta\Providers;

use Illuminate\Support\ServiceProvider;

use View;
use Form;
use Response;
use Queue;


use TopBetta\Helpers\LibSimpleXMLElement;
use \Illuminate\Contracts\Support\Arrayable;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'TopBetta\Services\Registrar'
		);

		/*
|--------------------------------------------------------------------------
| Admin views
|--------------------------------------------------------------------------
*/

//		View::addLocation(app('path').'/topbetta/admin/views');
//		View::addNamespace('admin', app('path').'/topbetta/admin/views');
//
//
//		/*
//		 * TopBetta Application Views
//		 */
//		View::addLocation(app('path').'/topbetta/Views');
//		View::addNamespace('topbetta', app('path').'/topbetta/Views');

		/**
		 * This event is fired when a queued job fails.
		 *
		 * If we define a method on the job class called 'failed' it will be called at this time.
		 * This allows us to define job specific actions on failure.
		 */
//		Queue::failing(function($connection, $job, $data)
//		{
//			Log::debug('Failed Job: '.print_r($data,true));
//			// if we have a job(class) and it has a failed method we run it
//			if(isset($data['job'])){
//				if(in_array('failed', get_class_methods($data['job']))){
//					$appClass = App::make($data['job']);
//					$appClass->failed($data['data']);
//				}
//			}
//
//
//		});

		Response::macro('xml', function($vars, $status = 200, array $header = array(), $rootElement = 'response', $xml = null)
		{

			if (is_object($vars) && $vars instanceof Arrayable) {
				$vars = $vars->toArray();
			}

			if (is_null($xml)) {
				$xml = new LibSimpleXMLElement('<' . $rootElement . '/>');
			}
			foreach ($vars as $key => $value) {
				if (is_array($value)) {
					if (is_numeric($key)) {
						Response::xml($value, $status, $header, $rootElement, $xml->addChild(str_singular($xml->getName())));
					} else {
						Response::xml($value, $status, $header, $rootElement, $xml->addChild($key));
					}
				} else {
					$xml->addChild($key, $value);
				}
			}
			if (empty($header)) {
				$header['Content-Type'] = 'application/xml';
			}
			return Response::make($xml->asXML(), $status, $header);
		});
	}

}
