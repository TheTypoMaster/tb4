<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/07/2015
 * Time: 4:17 PM
 */

namespace TopBetta\Resources\Tournaments;

use TopBetta\Resources\AbstractEloquentResource;

class TournamentEventGroupResource extends AbstractEloquentResource {

    protected $attributes = array(
        'id' => 'id',
        'name' => 'group_name',
        'description' => 'description',
        'ordering' => 'ordering',
        'group_icon' => 'tournament_group_icon',
    );

    protected $loadIfRelationExists = array(
        'tournaments' => 'tournaments'
    );

    protected static $modelClass = 'TopBetta\Models\TournamentEventGroupModel';

    public function tournaments()
    {
        return $this->collection('tournaments', 'TopBetta\Resources\Tournaments\TournamentResource', 'tournaments');
    }
}