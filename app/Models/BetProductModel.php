<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 22:25
 * Project: tb4
 */

use Eloquent;

class BetProductModel extends Eloquent{

    protected $table = 'tbdb_bet_product';
    protected $guarded = array();

    public static $rules = array();

    public function productProviderMatch()
    {
        return $this->hasOne('TopBetta\Models\ProductProviderMatch', 'tb_product_id');
    }

} 