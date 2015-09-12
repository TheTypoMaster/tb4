<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 10/08/2015
 * Time: 3:42 PM
 */

namespace TopBetta\Services\Tournaments\Resulting;


use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\FreeCreditTransactionTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Accounting\FreeCreditTransactionService;
use TopBetta\Services\Tournaments\Email\TournamentEmailService;
use TopBetta\Services\Tournaments\Exceptions\TournamentResultedException;
use TopBetta\Services\Tournaments\TournamentService;

/**
 * Class TournamentResulter
 * Pays out tournaments
 * @package TopBetta\Services\Tournaments\Resulting
 */
class TournamentResulter {

    /**
     * @var TournamentResultService
     */
    private $resultService;
    /**
     * @var AccountTransactionService
     */
    private $accountTransactionService;
    /**
     * @var FreeCreditTransactionService
     */
    private $freeCreditTransactionService;
    /**
     * @var TournamentTicketRepositoryInterface
     */
    private $ticketRepository;
    /**
     * @var TournamentService
     */
    private $tournamentService;
    /**
     * @var TournamentEmailService
     */
    private $emailService;

    public function __construct(TournamentResultService $resultService,
                                AccountTransactionService $accountTransactionService,
                                FreeCreditTransactionService $freeCreditTransactionService,
                                TournamentService $tournamentService,
                                TournamentTicketRepositoryInterface $ticketRepository,
                                TournamentEmailService $emailService)
    {
        $this->resultService = $resultService;
        $this->accountTransactionService = $accountTransactionService;
        $this->freeCreditTransactionService = $freeCreditTransactionService;
        $this->ticketRepository = $ticketRepository;
        $this->tournamentService = $tournamentService;
        $this->emailService = $emailService;
    }

    /**
     * Get tournament results and payout
     * @param $tournament
     * @throws TournamentResultedException
     */
    public function resultTournament($tournament)
    {
        if ($tournament->paid_flag) {
            throw new TournamentResultedException("Tournament " .$tournament->name . " is already resulted");
        }

        if (!$tournament->qualifiers->count()) {
            \Log::info("Tournament " . $tournament->id . " has no qualifiers. Refunding.");
            $this->tournamentService->refundTournament($tournament);
            return;
        }

        try {
            $results = $this->resultService->getTournamentResults($tournament);
        } catch (\Exception $e) {
            \Log::error("Error getting tournament results for tournament" . $e->getMessage());
            return;
        }

        \Log::info("RESULTING TOURNAMENT " . $tournament->id);
        foreach ($results as $result) {
            $this->payResult($result);

            if ($tournament->email_flag) {
                $this->emailService->sendWinnerNotification($result);
            }
        }

        $this->tournamentService->setTournamentPaid($tournament);
    }

    /**
     * Payout a tournament result
     * @param TournamentResult $result
     */
    public function payResult(TournamentResult $result)
    {
        \Log::info("TournamentResulter: Paying ticket " . $result->getTicket()->id);
        if ($result->getJackpotTicket() && ! $result->jackpotTicketExists()) {
            $this->payTournamentTicketResult($result);
        }

        if ($result->getAmount()) {
            $this->payCashResult($result);
        }

        if ($result->getTotalFreeCreditAmount()) {
            $this->payFreeCreditResult($result);
        }
    }

    /**
     * Pay out cash
     * @param TournamentResult $result
     */
    public function payCashResult(TournamentResult $result)
    {
        $transaction = $this->accountTransactionService->increaseAccountBalance(
            $result->getTicket()->user->id,
            $result->getAmount(),
            AccountTransactionTypeRepositoryInterface::TYPE_TOURNAMENT_WIN
        );

        $this->ticketRepository->updateWithId($result->getTicket()->id, array(
            "result_transaction_id" => $transaction['id'],
        ));
    }

    /**
     * Payout free credit
     * @param TournamentResult $result
     */
    public function payFreeCreditResult(TournamentResult $result)
    {
        $transaction =$this->freeCreditTransactionService->increaseBalance(
            $result->getTicket()->user->id,
            $result->getTotalFreeCreditAmount(),
            FreeCreditTransactionTypeRepositoryInterface::TRANSACTION_TYPE_WIN
        );

        $this->ticketRepository->updateWithId($result->getTicket()->id, array(
            "result_transaction_id" => $transaction['id'],
        ));
    }

    /**
     * Give user tournament ticket
     * @param TournamentResult $result
     */
    public function payTournamentTicketResult(TournamentResult $result)
    {
        $this->tournamentService->createTicketAndLeaderboardForUser(
            $result->getJackpotTicket(),
            $result->getTicket()->user
        );
    }

}