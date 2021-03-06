<?php namespace TopBetta\Repositories\Contracts;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 19:26
 * Project: tb4
 */

interface SelectionResultRepositoryInterface {

    public function deleteResults($resultIds);

    public function getResultsForEvent($eventId);

    public function getResultsForEventByPosition($eventId, $position);
}