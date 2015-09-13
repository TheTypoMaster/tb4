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

    protected $attributes = array(
        'id'           => 'id',
        'username'     => 'username',
        'tournamentId' => 'tournament_id',
        'comment'      => 'comment',
        'date'         => 'date',
    );

    public function getUsername()
    {
        return $this->model->user ? $this->model->user->username : null;
    }

    public function getDate()
    {
        return $this->model->created_at->toDateTimeString();
    }
}