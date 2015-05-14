<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 11:15 AM
 */

namespace TopBetta\Services\Betting;


class EventService {

    public function isSelectionEventAvailableForBetting($selection)
    {
        return $this->isEventAvailableForBetting($selection->market->event);
    }

    public function isEventAvailableForBetting($event)
    {
        return $event->display_flag;
    }
}