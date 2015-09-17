<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 31/07/2015
 * Time: 3:04 PM
 */

namespace TopBetta\Services\Resources\Tournaments;


use TopBetta\Repositories\Contracts\TournamentCommentRepositoryInterface;
use TopBetta\Resources\PaginatedEloquentResourceCollection;

class CommentResourceService {

    /**
     * @var TournamentCommentRepositoryInterface
     */
    private $commentRepository;

    public function __construct(TournamentCommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function getComments($tournament, $limit = 50)
    {
        $comments = $this->commentRepository->getCommentsForTournament($tournament, $limit);

        if ($comments instanceof PaginatedEloquentResourceCollection) {
            return $comments;
        }

        return new PaginatedEloquentResourceCollection($comments, 'TopBetta\Resources\Tournaments\CommentResource');
    }
}