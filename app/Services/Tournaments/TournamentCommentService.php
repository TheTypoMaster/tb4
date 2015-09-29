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
use TopBetta\Services\UserAccount\UserAccountService;
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
                                 UserRepo $userRepository,
                                 UserAccountService $userService)
    {
        $this->commentRepository = $commentRepository;
        $this->tournamentRepository = $tournamentRepository;
        $this->commentResourceService = $commentResourceService;
        $this->ticketRepository = $ticketRepository;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
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
            "visible" => true,
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

        return $this->commentResourceService->getComments($tournament, array_get($data, 'limit', 50));
    }

    /**
     * get all the comments collections and transact them to array
     * @return mixed
     */
    public function getAllComments() {
        $comments = $this->commentRepository->getAllComments();
        $pagination = array();
        $pagination['total_pages'] = (int)ceil($comments->total() / 15);
        $pagination['current_page'] = $comments->currentPage();

        $pagination['has_more_pages'] = $comments->hasMorePages();
        $pagination['previous_page_url'] = $comments->previousPageUrl();
        $pagination['next_page_url'] = $comments->nextPageUrl();

        $comment_list = array();
        foreach($comments as $comment) {
            $comment_trans = array();
//            $user = UserModel::where('id', $comment->user_id)->first();
            $user = $this->userService->getUser($comment->user_id);
            $tournament = TournamentModel::where('id', $comment->tournament_id)->first();
            $comment_trans['id'] = $comment->id;


//            if($user->usertype == 'Super Administrator') {
//                $comment_trans['username'] = 'TopBetta Admin';
//            } else {
//                $comment_trans['username'] = $user->name;
//            }

            if($user->permissions) {

                if($user->permissions['superuser'] == 1) {
                    $comment_trans['username'] = 'TopBetta Admin';
                } else {
                    $comment_trans['username'] = $user->username;
                }

            } else {
                $comment_trans['username'] = $user->username;
            }


//            $comment_trans['username'] = $user->name;
            $comment_trans['tournament_id'] = $tournament->id;
            $comment_trans['tournament_name'] = $tournament->name;
            $comment_trans['buy_in'] = $tournament->buy_in;
            $comment_trans['entry_fee'] = $tournament->entry_fee;
            $comment_trans['created_date'] = $comment->created_date;
            $comment_trans['visible'] = $comment->visible;
            $comment_trans['comment'] = $comment->comment;
            array_push($comment_list, $comment_trans);
        }

        $comments_with_pagination = array();
        $comments_with_pagination['comment_list'] = $comment_list;
        $comments_with_pagination['pagination'] = $pagination;
        return $comments_with_pagination;
    }

    /**
     * get comment by id
     * @param $comment_id
     * @return mixed
     */
    public function getCommentById($comment_id) {
        return $this->commentRepository->getCommentById($comment_id);
    }

    public function searchComments($tournament_id, $username, $visibility) {
        $comments = $this->commentRepository->searchComments($tournament_id, $username, $visibility);
        $pagination = array();
        $pagination['total_pages'] = (int)ceil($comments->total() / 15);
        $pagination['current_page'] = $comments->currentPage();

        $pagination['has_more_pages'] = $comments->hasMorePages();
        $pagination['previous_page_url'] = $comments->previousPageUrl();
        $pagination['next_page_url'] = $comments->nextPageUrl();

        $comment_list = array();
        foreach($comments as $comment) {
            $comment_trans = array();
//            $user = UserModel::where('id', $comment->user_id)->first();
            $user = $this->userService->getUser($comment->user_id);
            $tournament = TournamentModel::where('id', $comment->tournament_id)->first();
            $comment_trans['id'] = $comment->id;


            if($user->permissions) {

                if($user->permissions['superuser'] == 1) {
                    $comment_trans['username'] = 'TopBetta Admin';
                } else {
                    $comment_trans['username'] = $user->username;
                }

            } else {
                $comment_trans['username'] = $user->username;
            }


//            $comment_trans['username'] = $user->name;
            $comment_trans['tournament_id'] = $tournament->id;
            $comment_trans['tournament_name'] = $tournament->name;
            $comment_trans['buy_in'] = $tournament->buy_in;
            $comment_trans['entry_fee'] = $tournament->entry_fee;
            $comment_trans['created_date'] = $comment->created_date;
            $comment_trans['visible'] = $comment->visible;
            $comment_trans['comment'] = $comment->comment;
            array_push($comment_list, $comment_trans);
        }

        $comments_with_pagination = array();
        $comments_with_pagination['comment_list'] = $comment_list;
        $comments_with_pagination['pagination'] = $pagination;
        return $comments_with_pagination;
}
}