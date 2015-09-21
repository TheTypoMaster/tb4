<?php namespace TopBetta\Services\Betting;
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 11:15 AM
 */

use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetResultStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentBetRepositoryInterface;

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
     * @var BetRepositoryInterface
     */
    private $betRepository;
    /**
     * @var TournamentBetRepositoryInterface
     */
    private $tournamentBetRepository;

    /**
     * @param EventStatusRepositoryInterface $eventStatusRepository
     * @param EventRepositoryInterface $eventRepository
     * @param BetRepositoryInterface $betRepository
     * @param TournamentBetRepositoryInterface $tournamentBetRepository
     */
    public function __construct(EventStatusRepositoryInterface $eventStatusRepository, EventRepositoryInterface $eventRepository, BetRepositoryInterface $betRepository, TournamentBetRepositoryInterface $tournamentBetRepository)
    {
        $this->eventStatusRepository = $eventStatusRepository;
        $this->eventRepository = $eventRepository;
        $this->betRepository = $betRepository;
        $this->tournamentBetRepository = $tournamentBetRepository;
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
        return $this->eventRepository->updateWithId($event->id, array(
            "paid_flag" => true,
            "event_status_id" => $this->eventStatusRepository->getByName(EventStatusRepositoryInterface::STATUS_PAID)->id,
        ));
    }

    public function setEventPaying($event, $paidFlag = false)
    {
        return $this->eventRepository->updateWithId($event->id, array(
            "event_status_id" => $this->eventStatusRepository->getByName(EventStatusRepositoryInterface::STATUS_PAYING)->id,
        ));
    }

    public function checkAndSetPaidStatus($event)
    {
        $betsPaid = false;
        if ( ! $this->betRepository->getBetsForEventByStatus($event->id, BetResultStatusRepositoryInterface::RESULT_STATUS_UNRESULTED)->count() &&
            ! $this->tournamentBetRepository->getBetsForEventByStatus($event->id, BetResultStatusRepositoryInterface::RESULT_STATUS_UNRESULTED)->count()
        ) {
            $this->setEventPaid($event);
            $betsPaid = true;
        }

        return $betsPaid;
    }

    /**
     * get all events start from today
     * @return mixed
     */
    public function getAllEventsFromToday() {
        $events = $this->eventRepository->getAllEventsFromToday();
        $event_list = array();

        foreach($events as $event) {
            $event_list[$event->id] = '(#' . $event->id . ') ' . $event->name;
        }
        return $event_list;
    }

    public function getEventByID($id) {
        $event = $this->eventRepository->getEventByEventID($id);
        return $event;
    }

}