<?php namespace TopBetta\Providers;
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
            'TopBetta\Repositories\Contracts\BetRepositoryInterface',
            'TopBetta\Repositories\DbBetRepository'
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
        $this->app->bind(
            'TopBetta\Repositories\Contracts\PoliTransactionRepositoryInterface',
            'TopBetta\Repositories\DbPoliTransactionRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface',
            'TopBetta\Repositories\DbMarketTypeRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\MarketRepositoryInterface',
            'TopBetta\Repositories\DbMarketRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\SelectionStatusRepositoryInterface',
            'TopBetta\Repositories\DbSelectionStatusRepository'
		);
		$this->app->bind(
            'TopBetta\Repositories\Contracts\FreeCreditTransactionRepositoryInterface',
            'TopBetta\Repositories\DbFreeCreditTransactionRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\ProcessParamsRepositoryInterface',
            'TopBetta\Repositories\DbProcessParamsRepository'
		);
		$this->app->bind(
            'TopBetta\Repositories\Contracts\EventModelRepositoryInterface',
            'TopBetta\Repositories\DbEventModelRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\WithdrawalRequestRepositoryInterface',
            'TopBetta\Repositories\DbWithdrawalRequestRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\PromotionRepositoryInterface',
            'TopBetta\Repositories\DbPromotionRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\SportRepositoryInterface',
            'TopBetta\Repositories\DbSportsRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\MarketOrderingRepositoryInterface',
            'TopBetta\Repositories\DbMarketOrderingRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface',
            'TopBetta\Repositories\DbBaseCompetitionRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\IconRepositoryInterface',
            'TopBetta\Repositories\DbIconRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\IconTypeRepositoryInterface',
            'TopBetta\Repositories\DbIconTypeRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\TeamRepositoryInterface',
            'TopBetta\Repositories\DbTeamRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\PlayersRepositoryInterface',
            'TopBetta\Repositories\DbPlayersRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\CompetitionRegionRepositoryInterface',
            'TopBetta\Repositories\DbCompetitionRegionRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\TournamentCompetitionRepositoryInterface',
            'TopBetta\Repositories\DbTournamentCompetiitonRepository'
		);
		$this->app->bind(
			'TopBetta\Repositories\Contracts\DataValueRepositoryInterface',
			'TopBetta\Repositories\DbDataValueRepository'
		);
		$this->app->bind(
			'TopBetta\Repositories\Contracts\TournamentRepositoryInterface',
			'TopBetta\Repositories\DbTournamentRepository'
		);
		$this->app->bind(
			'TopBetta\Repositories\Contracts\LastStartRepositoryInterface',
			'TopBetta\Repositories\DbLastStartRepository'
		);
		$this->app->bind(
			'TopBetta\Repositories\Contracts\SelectionPriceRepositoryInterface',
			'TopBetta\Repositories\DbSelectionPriceRepository'
		);
        $this->app->bind(
            'TopBetta\Repositories\Contracts\FreeCreditTransactionTypeRepositoryInterface',
            'TopBetta\Repositories\DbFreeCreditTransactionTypeRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\ConfigurationRepositoryInterface',
            'TopBetta\Repositories\DbConfigurationRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\TournamentBuyInRepositoryInterface',
            'TopBetta\Repositories\DbTournamentBuyInRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\TODRepositoryInterface',
            'TopBetta\Repositories\DbTODRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\TournamentLabelsRepositoryInterface',
            'TopBetta\Repositories\DbTournamentLabelsRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\TournamentPrizeFormatRepositoryInterface',
            'TopBetta\Repositories\DbTournamentPrizeFormatRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\TournamentTicketBuyInHistoryRepositoryInterface',
            'TopBetta\Repositories\DbTournamentTicketBuyInHistoryRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\TournamentBuyInTypeRepositoryInterface',
            'TopBetta\Repositories\DbTournamentBuyInTypeRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\TournamentRepositoryInterface',
            'TopBetta\Repositories\DbTournamentRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface',
            'TopBetta\Repositories\DbTournamentTicketRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\BetTypeRepositoryInterface',
            'TopBetta\Repositories\DbBetTypeRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\BetResultStatusRepositoryInterface',
            'TopBetta\Repositories\DbBetResultStatusRepository'
        );
		 $this->app->bind(
            'TopBetta\Repositories\Contracts\AdminGroupsRepositoryInterface',
            'TopBetta\Repositories\DbAdminGroupsRepository'
		);
		$this->app->bind(
            'TopBetta\Repositories\Contracts\TournamentBetRepositoryInterface',
            'TopBetta\Repositories\DbTournamentBetRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\BetResultStatusRepositoryInterface',
            'TopBetta\Repositories\DbBetResultStatusRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\BetTypeRepositoryInterface',
            'TopBetta\Repositories\DbBetTypeRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\PaymentEwayTokenRepositoryInterface',
            'TopBetta\Repositories\DbPaymentEwayTokenRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\ScheduledPaymentRepositoryInterface',
            'TopBetta\Repositories\DbScheduledPaymentRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\RunnerRepositoryInterface',
            'TopBetta\Repositories\DbRunnerRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\TrainerRepositoryInterface',
            'TopBetta\Repositories\DbTrainerRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\OwnerRepositoryInterface',
            'TopBetta\Repositories\DbOwnerRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\MeetingVenueRepositoryInterface',
            'TopBetta\Repositories\DbMeetingVenueRepository'
        );
		 $this->app->bind(
            'TopBetta\Repositories\Contracts\BetSelectionRepositoryInterface',
            'TopBetta\Repositories\DbBetSelectionRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\BetLimitRepositoryInterface',
            'TopBetta\Repositories\DbBetLimitRepository'
        );
        $this->app->bind(
            'TopBetta\Repositories\Contracts\BetLimitTypeRepositoryInterface',
            'TopBetta\Repositories\DbBetLimitTypeRepository'
		);
        $this->app->bind(
            'TopBetta\Repositories\Contracts\MarketModelRepositoryInterface',
            'TopBetta\Repositories\DbMarketModelRepository'
        );
    }

} 