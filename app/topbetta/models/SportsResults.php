<?php
namespace TopBetta;

class SportsResults extends \Eloquent {
    protected $guarded = array();

    public static $rules = array();

    public static function getResultsForEventId($eventId) {
    	return 'results for: ' . $eventId;
    }
}