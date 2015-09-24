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
        'name' => 'name',
        'type' => 'type',
        'start_date' => 'start_date',
        'end_date' => 'end_date',
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