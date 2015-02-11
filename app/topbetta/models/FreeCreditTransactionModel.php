<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/02/2015
 * Time: 10:08 AM
 */

namespace TopBetta\models;

use Eloquent;

class FreeCreditTransactionModel extends Eloquent {

    protected $table = 'tbdb_tournament_transaction';

    public function recipients()
    {
        return $this->belongsTo("TopBetta\\models\\UserModel", "recipient_id");
    }
}