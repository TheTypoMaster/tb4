<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 08:51
 * Project: tb4
 */

use Eloquent;

class EventStatusModel extends Eloquent{

    protected $table = 'tbdb_event_status';

    protected $guarded = array();

    public static $rules = array();

}