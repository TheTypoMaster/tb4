<?php namespace TopBetta\Services\Validation;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 14:48
 * Project: tb4
 */

use TopBetta\Services\Validation\Validator;

class TokentAuthenticationValidator extends Validator{

    protected $rules = array(
                            'club_name' => 'required|string',
                            'betting_account' => 'required|string',
                            'token' => 'required|string'
                        );

}