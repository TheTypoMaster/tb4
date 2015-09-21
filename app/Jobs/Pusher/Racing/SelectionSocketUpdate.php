<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/09/2015
 * Time: 3:08 PM
 */

namespace TopBetta\Jobs\Pusher\Racing;


use TopBetta\Resources\SelectionResource;

class SelectionSocketUpdate extends RaceSocketUpdate {

    protected $event = 'selection_update';

    public function handle(\Pusher $pusher)
    {
        $data = array("id" => $this->data['id'], "selections" => array());

        foreach ($this->data['selections'] as $selection) {
            $data['selections'][] = (new SelectionResource($selection))->toArray();
        }

        $this->data = $data;

        parent::handle($pusher);
    }
}