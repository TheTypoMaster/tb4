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

    public function getAllVisibleTournamentComments($tournament)
    {
        return $this->model
            ->where('tournament_id', $tournament)
            ->where('visible', true)
            ->orderBy('created_at', 'Desc')
            ->with('user')
            ->get();
    }

    /**
     * get all the comments in database
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllComments() {
        return $this->model->paginate(15);
//        return $this->model->all();
    }

    /**
     * get comment by id
     * @param $comment_id
     * @return mixed
     */
    public function getCommentById($comment_id) {
        return $this->model->find($comment_id);
    }
}