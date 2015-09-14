<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/07/2015
 * Time: 4:17 PM
 */

namespace TopBetta\Resources\Tournaments;


use TopBetta\Resources\AbstractEloquentResource;

class TournamentGroupResource extends AbstractEloquentResource {

    protected $attributes = array(
        'id' => 'id',
        'name' => 'group_name',
        'description' => 'description',
        'ordering' => 'ordering'
    );

    protected $loadIfRelationExists = array(
        'tournaments' => 'tournaments'
    );

    protected static $modelClass = 'TopBetta\Models\TournamentGroupModel';

    public function tournaments()
    {
        return $this->collection('tournaments', 'TopBetta\Resources\Tournaments\TournamentResource', 'tournaments');
    }
}