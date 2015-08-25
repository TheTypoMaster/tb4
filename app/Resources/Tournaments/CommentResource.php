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
        'id'       => 'id',
        'username' => 'user.username',
        'comment'  => 'comment',
        'date'     => 'created_at',
    );
}