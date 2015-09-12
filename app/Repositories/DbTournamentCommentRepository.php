<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 31/07/2015
 * Time: 2:43 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\TournamentCommentModel;
use TopBetta\Repositories\Contracts\TournamentCommentRepositoryInterface;

class DbTournamentCommentRepository extends BaseEloquentRepository implements TournamentCommentRepositoryInterface
{

    public function __construct(TournamentCommentModel $model)
    {
        $this->model = $model;
    }

    public function getCommentsForTournament($tournament, $limit = 50)
    {
        return $this->model
            ->where('tournament_id', $tournament)
            ->orderBy('created_at', 'DESC')
            ->with('user')
            ->paginate($limit);
    }
}