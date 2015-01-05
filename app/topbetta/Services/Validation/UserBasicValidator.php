<?php namespace TopBetta\Services\Validation;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 17:12
 * Project: tb4
 */

class UserBasicValidator extends Validator{

    /**
     * Base rules
     *
     * @var array
     */

    public $rules = array(
        'username' => 'alphadash|min:6|unique:tbdb_users',
        'name' => 'min:3|max:254',
        'email' => 'email|unique:tbdb_users',
        'password' => 'min:6',
        'usertype' => 'min:10',
        'gid' => 'numeric',
        'activation' => 'alphanum|max:100',
        'params' => 'aphanum',
        'isCorporate' => 'in:0,1',
        'isTopBetta' => 'in:0,1',

    );


    /**
     * Create rules
     *
     * @var array
     */
    protected $createRules = array(
        'username' => 'required',
        'name' => 'required',
        'email' => 'required',
        'password' => 'required',
        'usertype' => 'required',
        'gid' => 'required',
        'registerDate' => 'required',
        'lastVisitDate' => 'required',
    );

    /**
     * Rules for updating a user
     *
     * @var array
     */
    protected $updateRules = array(
        'user_id' => 'required'
    );

}