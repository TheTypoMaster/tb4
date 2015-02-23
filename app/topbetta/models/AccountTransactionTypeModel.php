<?php namespace TopBetta\Models;

/**
 * Coded by Oliver Shanahan
 * File creation date: 5/01/15
 * File creation time: 10:48
 * Project: tb4
 */

use Eloquent;


class AccountTransactionTypeModel extends Eloquent
{


    const TYPE_TOURNAMENT_DOLLARS   = 'tournamentdollars';
    const TYPE_TOURNAMENT_WIN       = 'tournementwin';
    const TYPE_PAYPAL_DEPOSIT       = 'paypaldeposit';
    const TYPE_BET_WIN              = 'betwin';
    const TYPE_BET_ENTRY            = 'betentry';
    const TYPE_EWAY_DEPOSIT         = 'ewaydepsosit';
    const TYPE_BANK_DEPOSIT         = 'bankdeposit';
    const TYPE_BPAY_DEPOSIT         = 'bpaydeposit';
    const TYPE_ADMIN                = 'admin';
    const TYPE_TESTING              = 'testing';
    const TYPE_ENTRY                = 'entry';
    const TYPE_BUY_IN               = 'buyin';
    const TYPE_BET_REFUND           = 'betrefund';
    const TYPE_CHARGE_BACK          = 'chargeback';
    const TYPE_PROMO                = 'promo';
    const TYPE_MONEYBOOKERS_DEPOSIT = 'moneybookersdeposit';
    const TYPE_TOURNAMENT_REFUND    = 'tournamentrefund';
    const TYPE_POLI_DEPOSIT         = 'polideposit';

    public static $depositTransactions  = array(
        self::TYPE_PAYPAL_DEPOSIT,
        self::TYPE_EWAY_DEPOSIT,
        self::TYPE_BPAY_DEPOSIT,
        self::TYPE_BANK_DEPOSIT,
        self::TYPE_POLI_DEPOSIT,
        self::TYPE_MONEYBOOKERS_DEPOSIT,
    );

    protected $table = 'tbdb_account_transaction_type';
    protected $guarded = array();

}