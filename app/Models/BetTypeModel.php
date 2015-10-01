<?php namespace TopBetta\Models; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 10:35
 * Project: tb4
 */

use Eloquent;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class BetTypeModel extends Eloquent {

    protected $table = 'tbdb_bet_type';
    protected $guarded = array();


    public function isExotic()
    {
        return in_array($this->name, array(
            BetTypeRepositoryInterface::TYPE_QUINELLA,
            BetTypeRepositoryInterface::TYPE_EXACTA,
            BetTypeRepositoryInterface::TYPE_TRIFECTA,
            BetTypeRepositoryInterface::TYPE_FIRSTFOUR,
        ));
    }
}