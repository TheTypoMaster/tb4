<?php namespace TopBetta\Services\Html;

use Collective\Html\HtmlServiceProvider as CollectiveHtmlServiceProvider;

/**
 * Coded by Oliver Shanahan
 * File creation date: 18/05/15
 * File creation time: 14:16
 * Project: tb5
 */
 
class HtmlServiceProvider extends CollectiveHtmlServiceProvider {

	protected function registerFormBuilder()
	{
		$this->app->bindShared('form', function($app)
		{
			$form = new FormBuilder($app['html'], $app['url'], $app['session.store']->getToken());

			return $form->setSessionStore($app['session.store']);
		});


	}

    public function registerHtmlBuilder()
    {
        $this->app->bindShared('html', function($app)
        {
            return new HtmlBuilder($app['url']);
        });
    }

}