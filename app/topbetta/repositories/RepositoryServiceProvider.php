<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 2/12/14
 * File creation time: 6:23
 * Project: tb4
 */

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'Serena\Repositories\Contracts\BetProductRepositoryInterface',
            'Serena\Repositories\DbBetProductRepository'
        );
        $this->app->bind(
            'Serena\Repositories\Contracts\CompetitionRepositoryInterface',
            'Serena\Repositories\DbCompetitionRepository'
        );
        $this->app->bind(
            'Serena\Repositories\Contracts\EventRepositoryInterface',
            'Serena\Repositories\DbEventRepository'
        );
        $this->app->bind(
            'Serena\Repositories\Contracts\SelectionRepositoryInterface',
            'Serena\Repositories\DbSelectionRepository'
        );
        $this->app->bind(
            'Serena\Repositories\Contracts\SelectionResultRepositoryInterface',
            'Serena\Repositories\DbSelectionResultRepository'
        );
    }
} 