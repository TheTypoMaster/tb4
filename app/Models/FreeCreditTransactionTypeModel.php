<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 31/03/2015
 * Time: 4:31 PM
 */

namespace TopBetta\models;

use Eloquent;

class FreeCreditTransactionTypeModel extends Eloquent {

    protected $table = 'tbdb_tournament_transaction_type';

    protected $guarded = array();

    public static $rules = array();
}