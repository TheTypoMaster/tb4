<?php namespace TopBetta\Models; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 3/02/15
 * File creation time: 14:29
 * Project: tb4
 */

use Eloquent;

class UserAroMapModel extends Eloquent {
    protected $table = 'tbdb_core_acl_groups_aro_map';
    protected $guarded = array();
    public $timestamps = false;

}