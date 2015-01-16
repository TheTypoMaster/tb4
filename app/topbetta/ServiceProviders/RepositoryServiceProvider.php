<?php namespace TopBetta\ServiceProviders;
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
            'TopBetta\Repositories\Contracts\BetProductRepositoryInterface',
            'TopBetta\Repositories\DbBetProductRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\CompetitionRepositoryInterface',
            'TopBetta\Repositories\DbCompetitionRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\EventRepositoryInterface',
            'TopBetta\Repositories\DbEventRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\SelectionRepositoryInterface',
            'TopBetta\Repositories\DbSelectionRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\SelectionResultRepositoryInterface',
            'TopBetta\Repositories\DbSelectionResultRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\EventStatusRepositoryInterface',
            'TopBetta\Repositories\DbEventStatusRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\EventRepositoryInterface',
            'TopBetta\Repositories\DbEventRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\BetOriginRepositoryInterface',
            'TopBetta\Repositories\DbBetOriginRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\BetSourceRepositoryInterface',
            'TopBetta\Repositories\DbBetSourceRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\UserTokenRepositoryInterface',
            'TopBetta\Repositories\DbUserTokenRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\UserRepositoryInterface',
            'TopBetta\Repositories\DbUserRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\UserTopBettaRepositoryInterface',
            'TopBetta\Repositories\DbUserTopbettaRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface',
            'TopBetta\Repositories\DbAccountTransactionRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface',
            'TopBetta\Repositories\DbAccountTransactionTypeRepository'
        );
    }
} 