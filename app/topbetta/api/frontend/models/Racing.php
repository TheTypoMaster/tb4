<?php namespace TopBetta\frontend;

class Racing extends \Eloquent {
    protected $guarded = array();

    public static $rules = array();
	
	protected $table = 'tbdb_event';
}