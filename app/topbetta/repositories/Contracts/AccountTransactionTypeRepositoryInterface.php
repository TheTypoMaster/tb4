<?php namespace TopBetta\Repositories\Contracts;
/**
 * Coded by Oliver Shanahan
 * File creation date: 5/01/15
 * File creation time: 10:51
 * Project: tb4
 */


interface AccountTransactionTypeRepositoryInterface {

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


}