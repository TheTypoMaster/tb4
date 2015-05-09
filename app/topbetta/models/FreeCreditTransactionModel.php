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

    protected $fillable = array(
        "recipient_id",
        "giver_id",
        "session_tracking_id",
        "tournament_transaction_type_id",
        "amount",
        "notes",
        "created_date",
    );

    public function recipients()
    {
        return $this->belongsTo("TopBetta\\models\\UserModel", "recipient_id");
    }

    public function giver()
    {
        return $this->belongsTo('TopBetta\models\UserModel', 'giver_id');
    }

    public function transactionType()
    {
        return $this->belongsTo('TopBetta\models\FreeCreditTransactionTypeModel', 'tournament_transaction_type_id');
    }
}