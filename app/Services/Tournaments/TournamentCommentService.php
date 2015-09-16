<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 31/07/2015
 * Time: 2:48 PM
 */

namespace TopBetta\Services\Tournaments;

use Config;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use TopBetta\Models\TournamentModel;
use TopBetta\Models\UserModel;
use TopBetta\Repositories\Contracts\TournamentCommentRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Repositories\UserRepo;
use TopBetta\Services\Resources\Tournaments\CommentResource;
use TopBetta\Services\Resources\Tournaments\CommentResourceService;
use TopBetta\Services\Validation\TournamentCommentValidator;

class TournamentCommentService {

    /**
     * @var TournamentCommentRepositoryInterface
     */
    private $commentRepository;
    /**
     * @var TournamentRepositoryInterface
     */
    private $tournamentRepository;
    /**
     * @var CommentResourceService
     */
    private $commentResourceService;
    /**
     * @var TournamentTicketRepositoryInterface
     */
    private $ticketRepository;

    public function  __construct(TournamentCommentRepositoryInterface $commentRepository,
                                 TournamentRepositoryInterface $tournamentRepository,
                                 CommentResourceService $commentResourceService,
                                 TournamentTicketRepositoryInterface $ticketRepository,
                                 UserRepo $userRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->tournamentRepository = $tournamentRepository;
        $this->commentResourceService = $commentResourceService;
        $this->ticketRepository = $ticketRepository;
        $this->userRepository = $userRepository;
    }

    public function storeComment($user, $data)
    {
        $validator = new TournamentCommentValidator;

        $validator->validateForCreation($data);

        $tournament = $this->tournamentRepository->find($data['tournament_id']);

        if( ! $tournament ) {
            throw new ModelNotFoundException("Tournament not found");
        }

        $ticket = $this->ticketRepository->getTicketByUserAndTournament($user->id, $tournament->id);

        if( ! $ticket ) {
            throw new UnauthorizedException("Must be playing tournament to comment");
        }

        return $this->createComment($user, $tournament, $data['comment']);
    }

    public function createComment($user, $tournament, $comment)
    {
        if( ! $this->canComment($tournament) ) {
            throw new \Exception;
        }

        foreach(Config::get('tournament.comment-censored-words') as $word) {
            $comment = str_ireplace($word, str_repeat('*', strlen($word)), $comment);
        }

        $comment =  $this->commentRepository->create(array(
            "tournament_id" => $tournament->id,
            "user_id" => $user->id,
            "comment" => $comment,
        ));

        return $this->commentRepository->find($comment['id']);
    }

    public function canComment($tournament)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $tournament->end_date)->addDays(2) >= Carbon::now();
    }

    public function getComments($data)
    {
        if( ! $tournament = array_get($data, 'tournament_id') ) {
            throw new \InvalidArgumentException("No tournament id specified");
        }

        return $this->commentResourceService->getComments($tournament, array_get($data, 'limit'));
    }

    /**
     * get all the comments in database
     * @return mixed
     */
    public function getAllComments() {
        $comments = $this->commentRepository->getAllComments();
        $comment_list = array();
        foreach($comments as $comment) {
            $comment_trans = array();
            $comment_trans['id'] = $comment->id;
            $comment_trans['username'] = UserModel::where('id', $comment->user_id)->first()->name;
            $comment_trans['tournament'] = TournamentModel::where('id', $comment->tournament_id)->first()->name;
            $comment_trans['created_date'] = $comment->created_date;
            $comment_trans['comment'] = $comment->comment;
            array_push($comment_list, $comment_trans);
        }
        return $comment_list;
    }
}