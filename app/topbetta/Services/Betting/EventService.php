<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 11:15 AM
 */

namespace TopBetta\Services\Betting;


use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;

class EventService {

    /**
     * @var EventStatusRepositoryInterface
     */
    private $eventStatusRepository;

    /**
     * @param EventStatusRepositoryInterface $eventStatusRepository
     */
    public function __construct(EventStatusRepositoryInterface $eventStatusRepository)
    {
        $this->eventStatusRepository = $eventStatusRepository;
    }

    public function isSelectionEventAvailableForBetting($selection)
    {
        return $this->isEventAvailableForBetting($selection->market->event);
    }

    public function isEventAvailableForBetting($event)
    {
        return $event->display_flag && ($event->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_SELLING || $event->override_start);
    }

    public static function isEventInternational($event)
    {
        return $event->competition->first()->country !== 'AU' && $event->competition->first()->country !== 'NZ';
    }
}