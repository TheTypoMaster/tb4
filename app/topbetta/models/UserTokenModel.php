<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 12:08
 * Project: tb4
 */

use Eloquent;

class UserTokenModel extends Eloquent {

    protected $table = 'tb_user_tokens';
    protected $guarded = array();
    public static $rules = array();

}