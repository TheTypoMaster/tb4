<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/02/2015
 * Time: 10:08 AM
 */

namespace TopBetta\Models;

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
        return $this->belongsTo('TopBetta\Models\UserModel', 'giver_id');
    }

    public function transactionType()
    {
        return $this->belongsTo('TopBetta\Models\FreeCreditTransactionTypeModel', 'tournament_transaction_type_id');
    }

    public function bet()
    {
        return $this->hasOne('TopBetta\Models\BetModel', 'bet_freebet_transaction_id');
    }

    public function tournamentEntry()
    {
        return $this->hasOne('TopBetta\Models\TournamentTicketModel', 'entry_fee_transaction_id');
    }

    public function tournamentBuyin()
    {
        return $this->hasOne('TopBetta\Models\TournamentTicketModel', 'buy_in_transaction_id');
    }
}