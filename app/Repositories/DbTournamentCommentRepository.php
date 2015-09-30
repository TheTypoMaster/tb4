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
            ->where('visible', 1)
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
        return $this->model->orderBy('created_at', 'DESC')->paginate(15);
    }

    /**
     * get comment by id
     * @param $comment_id
     * @return mixed
     */
    public function getCommentById($comment_id) {
        return $this->model->find($comment_id);
    }


    /**
     * search comments
     * @param $tournament_id
     * @param $username
     * @param $visibility
     * @return mixed
     */
    public function searchComments($tournament_id, $username, $visibility) {


        if($tournament_id == null && $username == null && $visibility == null) {
            $comments = $this->getAllComments();
        } else {
            $query = $this->model;
            if($tournament_id != null) {
                $query = $query->where('tournament_id', $tournament_id);
            }
            if($username != null) {

                $query = $query->from('tbdb_tournament_comment')
                    ->leftJoin('tbdb_users', 'tbdb_tournament_comment.user_id', '=', 'tbdb_users.id')
                    ->where('tbdb_users.username', 'like', '%'.$username.'%')
                    ->select(array('tbdb_tournament_comment.*'));
//                  ->select(array('tbdb_tournament_comment.*', 'tbdb_users.name as user_name'));

            }
            if($visibility != null) {
                $query = $query->where('visible', '1');
            }

            $comments = $query->orderBy('tbdb_tournament_comment.created_at', 'DESC')->paginate();
        }

        return $comments;
    }
}