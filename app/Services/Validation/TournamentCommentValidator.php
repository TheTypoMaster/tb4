<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 31/07/2015
 * Time: 2:57 PM
 */

namespace TopBetta\Services\Validation;


class TournamentCommentValidator extends Validator {

    public $rules = array(
        'tournament_id' => 'required',
        'comment' => 'required'
    );

    public $createRules = array();

    public $updateRules = array();
}