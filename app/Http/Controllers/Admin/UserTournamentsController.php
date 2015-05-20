<?php

namespace TopBetta\admin\controllers;

use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\FreeCreditTransactionRepositoryInterface;
use TopBetta\Repositories\DbTournamentLeaderboardRepository;
use TopBetta\Repositories\DbTournamentTicketRepository;
use TopBetta\Repositories\UserRepo;
use View;
use User;

class UserTournamentsController extends \BaseController
{

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var UserRepo
	 */
	private $userRepo;
    /**
     * @var DbTournamentLeaderboardRepository
     */
    private $leaderboardRepository;
    /**
     * @var DbTournamentTicketRepository
     */
    private $ticketRepository;
    /**
     * @var FreeCreditTransactionRepositoryInterface
     */
    private $creditTransactionRepository;
    /**
     * @var AccountTransactionRepositoryInterface
     */
    private $accountTransactionRepository;

    public function __construct(User $user, UserRepo $userRepo,
                                DbTournamentLeaderboardRepository $leaderboardRepository,
                                DbTournamentTicketRepository $ticketRepository,
                                FreeCreditTransactionRepositoryInterface $creditTransactionRepository,
                                AccountTransactionRepositoryInterface $accountTransactionRepository)
	{

		$this->userRepo = $userRepo;
		$this->user = $user;
        $this->leaderboardRepository = $leaderboardRepository;
        $this->ticketRepository = $ticketRepository;
        $this->creditTransactionRepository = $creditTransactionRepository;
        $this->accountTransactionRepository = $accountTransactionRepository;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($userId)
	{
		$user = $this->user->find($userId);
		$tournaments = $this->userRepo->tournaments($user->id);

        //get the position and prize
        $tournamentInfo = array();
        foreach($tournaments as $tournament) {
            $tourn = array();
            $tourn['tournament'] = $tournament;

            $tourn['position'] = $this->leaderboardRepository->getLeaderBoardPositionForUser($userId, $tournament->id);

            $ticket = $this->ticketRepository->getTicketByUserAndTournament($userId, $tournament->id);

            //get the prize
            $tourn['prize'] = 0;
            $tourn['free_credit_prize']=0;
            if($ticket->result_transaction_id && ! $ticket->refunded_flag) {
                if($ticket->tournament->free_credit_flag) {
                    $prize = $this->creditTransactionRepository->find($ticket->result_transaction_id);
                    $tourn['free_credit_prize'] = $prize ? $prize->amount : 0;
                } else {
                    $prize = $this->accountTransactionRepository->find($ticket->result_transaction_id);
                    $tourn['prize'] = $prize ? $prize->amount : 0;
                }
            }

            $tournamentInfo[] = $tourn;
        }

		return View::make('admin::tournaments.user.index')
						->with(compact('user', 'tournamentInfo', 'tournaments'))
						->with('active', 'tournaments');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
