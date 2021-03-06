<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 31/07/2015
 * Time: 3:02 PM
 */

namespace TopBetta\Resources\Tournaments;


use TopBetta\Resources\AbstractEloquentResource;

class CommentResource extends AbstractEloquentResource
{
    protected static $modelClass = 'TopBetta\Models\TournamentCommentModel';

    protected $attributes = array(
        'id'           => 'id',
        'username'     => 'username',
        'user_id'      => 'user_id',
        'tournamentId' => 'tournament_id',
        'comment'      => 'comment',
        'date'         => 'date',
    );

    public function getUsername()
    {
        if ($this->model->username) {
            return $this->model->username;
        }

        return $this->model->user ? $this->model->user->username : null;
    }

    public function getDate()
    {
        if ($this->model->date) {
            return $this->model->date;
        }

        return $this->model->created_at->toDateTimeString();
    }
}