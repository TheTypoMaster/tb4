<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 31/03/2015
 * Time: 4:35 PM
 */
namespace TopBetta\Repositories\Contracts;

interface FreeCreditTransactionTypeRepositoryInterface
{
    const TRANSACTION_TYPE_ENTRY           = 'entry';
    const TRANSACTION_TYPE_BUYIN           = 'buyin';
    const TRANSACTION_TYPE_WIN             = 'win';
    const TRANSACTION_TYPE_REFUND          = 'refund';
    const TRANSACTION_TYPE_PROMO           = 'promo';
    const TRANSACTION_TYPE_TESTING         = 'testing';
    const TRANSACTION_TYPE_ADMIN           = 'admin';
    const TRANSACTION_TYPE_PURCHASE        = 'purchase';
    const TRANSACTION_TYPE_REFERRAL        = 'referral';
    const TRANSACTION_TYPE_NO_QUALIFIERS   = 'noqualifiers';
    const TRANSACTION_TYPE_FREE_BET_ENTRY  = 'freebetentry';
    const TRANSACTION_TYPE_FREE_BET_REFUND = 'freebetrefund';
    const TRANSACTION_TYPE_REBUY_BUYIN     = 'rebuybuyin';
    const TRANSACTION_TYPE_REBUY_ENTRY     = 'rebuyentry';
    const TRANSACTION_TYPE_TOPUP_BUYIN     = 'topupbuyin';
    const TRANSACTION_TYPE_TOPUP_ENTRY     = 'topupentry';
    const TRANSACTION_TYPE_FREE_BET_PARTIAL_REFUND = "freebetpartialrefund";

    public function getIdByName($name);
}