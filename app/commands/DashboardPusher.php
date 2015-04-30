<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use TopBetta\Services\DashboardNotification\BetDashboardNotificationService;
use TopBetta\Services\DashboardNotification\TournamentDashboardNotificationService;
use TopBetta\Services\DashboardNotification\UserDashboardNotificationService;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface as TransactionType;
use TopBetta\Repositories\Contracts\FreeCreditTransactionTypeRepositoryInterface as FreeTransactionType;
use TopBetta\Models\BetModel;
use TopBetta\models\FreeCreditTransactionModel;
use TopBetta\Repositories\Contracts\FreeCreditTransactionRepositoryInterface;

class DashboardPusher extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'topbetta:push-to-dashboard';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Pushes data to dashboard system.';
    /**
     * @var BetDashboardNotificationService
     */
    private $betNotificationService;
    /**
     * @var TournamentDashboardNotificationService
     */
    private $tournamentNotificationService;
    /**
     * @var UserDashboardNotificationService
     */
    private $userNotificationService;
    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;
    /**
     * @var AccountTransactionRepositoryInterface
     */
    private $accountTransactionRepository;
    /**
     * @var FreeCreditTransactionRepositoryInterface
     */
    private $freeCreditTransactionRepository;

    /**
     * Create a new command instance.
     *
     * @param BetDashboardNotificationService $betNotificationService
     * @param TournamentDashboardNotificationService $tournamentNotificationService
     * @param UserDashboardNotificationService $userNotificationService
     * @param AccountTransactionRepositoryInterface $accountTransactionRepository
     * @param BetRepositoryInterface $betRepository
     * @param FreeCreditTransactionRepositoryInterface $freeCreditTransactionRepository
     */
	public function __construct(BetDashboardNotificationService $betNotificationService,
                                TournamentDashboardNotificationService $tournamentNotificationService,
                                UserDashboardNotificationService $userNotificationService,
                                AccountTransactionRepositoryInterface $accountTransactionRepository,
                                BetRepositoryInterface $betRepository,
                                FreeCreditTransactionRepositoryInterface $freeCreditTransactionRepository)
	{
		parent::__construct();
        $this->betNotificationService = $betNotificationService;
        $this->tournamentNotificationService = $tournamentNotificationService;
        $this->userNotificationService = $userNotificationService;
        $this->betRepository = $betRepository;
        $this->accountTransactionRepository = $accountTransactionRepository;
        $this->freeCreditTransactionRepository = $freeCreditTransactionRepository;
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $start = $this->option('start');
        $end = $this->option('end');

        \Log::info("ACCOUNT TRANSACTIONS");
		$transactions = $this->accountTransactionRepository->findAllWithTypePaged($page=1, $count=500, $start, $end);

        while(count($transactions)) {
            foreach ($transactions as $transaction) {
                \Log::info("TRANSACTION DATE : " . $transaction->created_date . " ID : " . $transaction->id);
                if(! $transaction->recipient ) continue;

                try {
                    switch ($transaction->transactionType->keyword) {
                        case TransactionType::TYPE_BET_ENTRY:
                            $bet = BetModel::where('bet_transaction_id', $transaction->id)->first();
                            $this->betNotificationService->notify(array('id' => $bet->id, "transactions" => array($transaction->id)));
                            break;

                        case TransactionType::TYPE_BET_WIN:
                            $bet = BetModel::where('result_transaction_id', $transaction->id)->first();
                            $this->betNotificationService->notify(array('id' => $bet->id, "transactions" => array($transaction->id)));
                            break;

                        case TransactionType::TYPE_BET_REFUND:
                            $bet = BetModel::where('refund_transaction_id', $transaction->id)->first();
                            $this->betNotificationService->notify(array('id' => $bet->id, "transactions" => array($transaction->id)));
                            break;

                        case TransactionType::TYPE_BUY_IN:
                            //hacky way to get the tournament ticket id

                            $tournTransaction = FreeCreditTransactionModel::where('recipient_id', $transaction->recipient_id)
                                ->where('amount', $transaction->amount)
                                ->where('tournament_transaction_type_id', 2)
                                ->where('created_date', '<=', \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $transaction->created_date)->addMinute()->toDateTimeString())
                                ->where('created_date', '>=', \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $transaction->created_date)->subMinute()->toDateTimeString())
                                ->first();

                            $ticket = \TopBetta\Models\TournamentTicketModel::where('buy_in_transaction_id', $tournTransaction->id)->first();

                            $this->tournamentNotificationService->notify(array("id" => $ticket->id, "transactions" => array($transaction->id)));

                            break;

                        case TransactionType::TYPE_ENTRY:
                            //hacky way to get the tournament ticket id
                            $tournTransaction = FreeCreditTransactionModel::where('recipient_id', $transaction->recipient_id)
                                ->where('amount', $transaction->amount)
                                ->where('tournament_transaction_type_id', 1)
                                ->where('created_date', '<=', \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $transaction->created_date)->addMinute()->toDateTimeString())
                                ->where('created_date', '>=', \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $transaction->created_date)->subMinute()->toDateTimeString())
                                ->first();

                            $ticket = \TopBetta\Models\TournamentTicketModel::where('entry_fee_transaction_id', $tournTransaction->id)->first();

                            $this->tournamentNotificationService->notify(array("id" => $ticket->id, "transactions" => array($transaction->id)));

                            break;

                        case TransactionType::TYPE_TOURNAMENT_WIN:
                            $ticket = \TopBetta\Models\TournamentTicketModel::join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_ticket.tournament_id')
                                ->where('result_transaction_id', $transaction->id)
                                ->where('free_credit_flag', 0)
                                ->select('tbdb_tournament_ticket.id AS id')
                                ->first();

                            $this->tournamentNotificationService->notify(array("id" => $ticket->id, "transactions" => array($transaction->id)));

                            break;

                        case TransactionType::TYPE_TOURNAMENT_REFUND:
                            $ticket = \TopBetta\Models\TournamentTicketModel::join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_ticket.tournament_id')
                                ->where('result_transaction_id', $transaction->id)
                                ->where('free_credit_flag', 0)
                                ->select('tbdb_tournament_ticket.id AS id')
                                ->first();

                            $this->tournamentNotificationService->notify(array("id" => $ticket->id, "transactions" => array($transaction->id)));

                            break;

                        default:
                            $this->userNotificationService->notify(array("id" => $transaction->recipient_id, "transactions" => array($transaction->id)));
                    }
                } catch (\Exception $e) {
                    \Log::error("ERROR DASHBOARD PUSHER TRANSACTION : " . $transaction->id . " MESSAGE : " . $e->getMessage());
                }
            }

            $transactions = $this->accountTransactionRepository->findAllWithTypePaged(++$page, $count, $start, $end);

        }

        \Log::info("FREE CREDIT TRANSACTIONS");
        $transactions = $this->freeCreditTransactionRepository->findAllPaged($page=1, $count=500, $start, $end);

        while(count($transactions)) {
            foreach ($transactions as $transaction) {
                \Log::info("TRANSACTION DATE : " . $transaction->created_date . " ID : " . $transaction->id);
                if(! $transaction->recipient ) continue;

                try {
                    switch ($transaction->transactionType->keyword) {
                        case FreeTransactionType::TRANSACTION_TYPE_FREE_BET_ENTRY:
                            $bet = BetModel::where('bet_freebet_transaction_id', $transaction->id)->first();
                            $this->betNotificationService->notify(array('id' => $bet->id, "transactions" => array($transaction->id)));
                            break;

                        case FreeTransactionType::TRANSACTION_TYPE_FREE_BET_REFUND:
                            $bet = BetModel::where('refund_freebet_transaction_id', $transaction->id)->first();
                            $this->betNotificationService->notify(array('id' => $bet->id, "transactions" => array($transaction->id)));
                            break;

                        case FreeTransactionType::TRANSACTION_TYPE_BUYIN:
                            $ticket = \TopBetta\Models\TournamentTicketModel::where('buy_in_transaction_id', $transaction->id)->first();

                            $this->tournamentNotificationService->notify(array("id" => $ticket->id, "transactions" => array($transaction->id)));

                            break;

                        case FreeTransactionType::TRANSACTION_TYPE_ENTRY:
                            $ticket = \TopBetta\Models\TournamentTicketModel::where('entry_fee_transaction_id', $transaction->id)->first();

                            $this->tournamentNotificationService->notify(array("id" => $ticket->id, "transactions" => array($transaction->id)));

                            break;

                        case FreeTransactionType::TRANSACTION_TYPE_WIN:
                            $ticket = \TopBetta\Models\TournamentTicketModel::join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_ticket.tournament_id')
                                ->where('result_transaction_id', $transaction->id)
                                ->where('free_credit_flag', 1)
                                ->select('tbdb_tournament_ticket.id AS id')
                                ->first();

                            $this->tournamentNotificationService->notify(array("id" => $ticket->id, "transactions" => array($transaction->id)));

                            break;

                        case FreeTransactionType::TRANSACTION_TYPE_REFUND:
                            $ticket = \TopBetta\Models\TournamentTicketModel::join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_ticket.tournament_id')
                                ->where('result_transaction_id', $transaction->id)
                                ->where('free_credit_flag', 1)
                                ->select('tbdb_tournament_ticket.id AS id')
                                ->first();

                            $this->tournamentNotificationService->notify(array("id" => $ticket->id, "transactions" => array($transaction->id)));

                            break;

                        case FreeTransactionType::TRANSACTION_TYPE_PURCHASE:
                            $tournTransaction = FreeCreditTransactionModel::where('recipient_id', $transaction->recipient_id)
                                ->where('amount', -$transaction->amount)
                                ->whereIn('tournament_transaction_type_id', array(1, 2))
                                ->where('created_date', '<=', \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $transaction->created_date)->addMinute()->toDateTimeString())
                                ->where('created_date', '>=', \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $transaction->created_date)->subMinute()->toDateTimeString())
                                ->first();

                            $transactionType = $tournTransaction->tournament_transaction_type_id == 1 ? 'entry_fee_transaction_id' : 'buy_in_transaction_id';

                            $ticket = \TopBetta\Models\TournamentTicketModel::where($transactionType, $tournTransaction->id)->first();

                            $this->tournamentNotificationService->notify(array("id" => $ticket->id, "transactions" => array($transaction->id)));

                            break;

                        default:
                            $this->userNotificationService->notify(array("id" => $transaction->recipient_id, "transactions" => array($transaction->id)));
                    }
                } catch (\Exception $e) {
                    \Log::error("ERROR DASHBOARD PUSHER TRANSACTION : " . $transaction->id . " MESSAGE : " . $e->getMessage());
                }
            }

            $transactions = $this->freeCreditTransactionRepository->findAllPaged(++$page, $count, $start, $end);
        }
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(

		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
            array('start', null, InputOption::VALUE_OPTIONAL, null, null),
            array('end', null, InputOption::VALUE_OPTIONAL, null, null),
		);
	}

}
