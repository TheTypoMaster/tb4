<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/09/2015
 * Time: 2:58 PM
 */

namespace TopBetta\Jobs\Pusher\Racing;


use TopBetta\Resources\SelectionResource;

class PriceSocketUpdate extends RaceSocketUpdate {

    protected $event = 'odds_update';

    public function handle(\Pusher $pusher)
    {
        $data = array('id' => $this->data['id'], 'selections' => array());
        foreach ($this->data['selections'] as $selection) {
            $resource = SelectionResource::createResourceFromArray($selection);
            $resource->loadRelation('prices');
            $data['selections'][] = $resource->toSmallArray();
        }

        $this->data = $data;

        parent::handle($pusher);
    }
}