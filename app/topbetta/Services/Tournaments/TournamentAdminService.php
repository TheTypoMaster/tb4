<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/05/2015
 * Time: 12:51 PM
 */

namespace TopBetta\Services\Tournaments;

use Auth;
use Carbon\Carbon;
use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Tournaments\Exceptions\TournamentBuyInException;
use TopBetta\Services\Tournaments\Exceptions\TournamentEntryException;

class TournamentAdminService {

    /**
     * @var TournamentTicketRepositoryInterface
     */
    private $tournamentTicketRepository;
    /**
     * @var AccountTransactionService
     */
    private $accountTransactionService;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var TournamentRepositoryInterface
     */
    private $tournamentRepository;

    public function __construct(TournamentRepositoryInterface $tournamentRepository, TournamentTicketRepositoryInterface $tournamentTicketRepository, AccountTransactionService $accountTransactionService, UserRepositoryInterface $userRepository)
    {
        $this->tournamentTicketRepository = $tournamentTicketRepository;
        $this->accountTransactionService = $accountTransactionService;
        $this->userRepository = $userRepository;
        $this->tournamentRepository = $tournamentRepository;
    }

    public function addUsersToTournamentByUsername($tournament, $usernames)
    {
        $userRepo = $this->userRepository;
        $userIds = array_map(function($value) use ($userRepo) {
            $userDetails = $userRepo->getUserDetailsFromUserName($value);
            return array_get($userDetails, 'id', null);
        }, $usernames);

        $this->addUsersToTournament($userIds, $tournament);
    }

    public function addUsersToTournament($userIds, $tournamentId)
    {
        $tournament = $this->tournamentRepository->find($tournamentId);

        //check tournament exists
        if( ! $tournament ) {
            throw new TournamentEntryException("Tournament " . $tournamentId . " does not exists");
        }

        //check tournament has not closed
        if( $tournament->entries_close < Carbon::now() ) {
            throw new TournamentEntryException("Entries closed at " . $tournament->entries_close);
        }

        foreach($userIds as $userId) {
            $this->addUserToTournament($tournament, $userId);
        }
    }

    public function addUserToTournament($tournament, $userId)
    {
        //check user doesn't have a ticket already
        if($this->tournamentTicketRepository->getTicketByUserAndTournament($userId, $tournament)) {
            throw new TournamentBuyInException("Tournament Ticket Already Exists");
        }

        $transaction = $this->accountTransactionService->increaseAccountBalance($userId, $tournament->buy_in + $tournament->entry_fee, AccountTransactionTypeRepositoryInterface::TYPE_PROMO, Auth::user()->id);


    }
}