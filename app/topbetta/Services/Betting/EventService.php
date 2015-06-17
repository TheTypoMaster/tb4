<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 11:15 AM
 */

namespace TopBetta\Services\Betting;


use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;

class EventService {

    private static $eventClosedStatuses = array(
        EventStatusRepositoryInterface::STATUS_INTERIM,
        EventStatusRepositoryInterface::STATUS_PAID,
        EventStatusRepositoryInterface::STATUS_PAYING,
        EventStatusRepositoryInterface::STATUS_CLOSED,
    );

    /**
     * @var EventStatusRepositoryInterface
     */
    private $eventStatusRepository;
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * @param EventStatusRepositoryInterface $eventStatusRepository
     * @param EventRepositoryInterface $eventRepository
     */
    public function __construct(EventStatusRepositoryInterface $eventStatusRepository, EventRepositoryInterface $eventRepository)
    {
        $this->eventStatusRepository = $eventStatusRepository;
        $this->eventRepository = $eventRepository;
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

    public function getExoticDividendsForEventByType($event, $type)
    {
        return unserialize($event->{$type . '_dividend'});
    }

    public function isEventPaying($event)
    {
        return $event->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_PAYING || $event->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_PAID;
    }

    public function isEventInterim($event)
    {
        return $event->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_INTERIM;
    }

    public function getClosedEventStatusIds()
    {
        return $this->eventStatusRepository->getStatusIds(self::$eventClosedStatuses);
    }

    public function setEventPaid($event)
    {
        return $this->eventRepository->updateWithId($event->id, array("paid_flag" => true));
    }

    public function setEventPaying($event, $paidFlag = false)
    {
        return $this->eventRepository->updateWithId($event->id, array(
            "event_status_id" => $this->eventStatusRepository->getByName(EventStatusRepositoryInterface::STATUS_PAYING)->id,
        ));
    }
}