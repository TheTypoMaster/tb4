<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/05/2015
 * Time: 12:51 PM
 */

namespace TopBetta\Services\Tournaments;

use Auth;
use Log;
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
    /**
     * @var TournamentService
     */
    private $tournamentService;

    public function __construct(TournamentRepositoryInterface $tournamentRepository,
                                TournamentTicketRepositoryInterface $tournamentTicketRepository,
                                AccountTransactionService $accountTransactionService,
                                UserRepositoryInterface $userRepository,
                                TournamentService $tournamentService)
    {
        $this->tournamentTicketRepository = $tournamentTicketRepository;
        $this->accountTransactionService = $accountTransactionService;
        $this->userRepository = $userRepository;
        $this->tournamentRepository = $tournamentRepository;
        $this->tournamentService = $tournamentService;
    }

    public function addUsersToTournamentByUsername($tournament, $usernames)
    {
        $userIds = array();
        $notFound = array();

        foreach($usernames as $username) {
            $user = $this->userRepository->getUserDetailsFromUsername($username);

            if( $user ) {
                $userIds[] = $user['id'];
            } else {
                $notFound[] = array("username" => $username, "reason" => "User not found");
            }
        }

        $result = $this->addUsersToTournament($userIds, $tournament);

        $result['not_entered'] = array_merge($result['not_entered'], $notFound);

        return $result;
    }

    public function addUsersToTournament($userIds, $tournamentId)
    {
        $tournament = $this->tournamentRepository->find($tournamentId);

        //check tournament exists
        if( ! $tournament ) {
            throw new TournamentEntryException("Tournament " . $tournamentId . " does not exists");
        }

        //check tournament has not closed
        if( ! $this->tournamentService->isTournamentOpen($tournament) ) {
            throw new TournamentEntryException("Entries closed");
        }

        $result = array("entered" => array(), "not_entered" => array());

        //add the users
        foreach($userIds as $userId) {
            $user = $this->userRepository->find($userId);

            try {
                $ticket = $this->addUserToTournament($tournament, $user);
                $result['entered'][] = array('id' => $user->id, 'username' => $user->username);
            } catch (TournamentEntryException $e) {
                Log::error("Tournament Entry Exception " . $e->getMessage());
                $result['not_entered'][] = array("id" => $user->id, 'username' => $user->username, "reason" => $e->getMessage());
            }
        }

        return $result;
    }

    public function addUserToTournament($tournament, $user)
    {
        //check user doesn't have a ticket already
        if($this->tournamentTicketRepository->getTicketByUserAndTournament($user->id, $tournament->id)) {
            throw new TournamentEntryException("Tournament Ticket Already Exists");
        }

        $transaction = $this->accountTransactionService->increaseAccountBalance($user->id, $tournament->buy_in + $tournament->entry_fee, AccountTransactionTypeRepositoryInterface::TYPE_PROMO, Auth::user()->id);

        if( ! $transaction ) {
            throw new TournamentEntryException("Error creating promo transaction");
        }

        try {
            $ticket = $this->tournamentService->enterUserInTournament($user, $tournament);
        } catch (TournamentBuyInException $e) {
            $this->accountTransactionService->decreaseAccountBalance($user->id, $transaction['amount'], AccountTransactionTypeRepositoryInterface::TYPE_ADMIN, Auth::user()->id);
            throw new TournamentEntryException("Error creating buyin/entry transaction");
        }

        return $ticket;
    }
}