<?php namespace TopBetta;

class HeartbeatStatus extends \Eloquent {
	protected $table = 'tb_heartbeat_status';
    protected $guarded = array();

    public static $rules = array();
}