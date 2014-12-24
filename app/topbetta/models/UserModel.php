<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 17:06
 * Project: tb4
 */

use Eloquent;

class UserModel extends Eloquent {

    protected $table = 'tbdb_users';
    protected $guarded = array();
    public static $rules = array();

}