<?php namespace TopBetta\Services\Validation;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 17:12
 * Project: tb4
 */

use TopBetta\Services\Validation\Validator;

class UserCreationValidator extends Validator{

    /**
     * Default rules
     *
     * @var array
     */
    protected $rules = array(
        'user_username' => 'required|Max:24|unique:tt_users',
        'user_first_name' => 'Max:128',
        'user_last_name' => 'Max:128',
        'user_email_address' => 'email|unique:tt_users|Max:128',
        'user_post_code' => 'Max:12',
        'user_dob' => 'Max:128',
        'user_password' => 'Max:256',
        'user_phone_number' => 'Max:24'

    );

    /**
     * Rules for updating a user
     *
     * @var array
     */
    protected $updateRules = array(
        'user_first_name' => 'Max:128',
        'user_last_name' => 'Max:128',
        'user_post_code' => 'Max:12',
        'user_dob' => 'Date',
        'user_phone_number' => 'Max:24'
    );

}