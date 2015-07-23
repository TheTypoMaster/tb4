<?php namespace TopBetta\Repositories\Contracts;
/**
 * Coded by Oliver Shanahan
 * File creation date: 5/01/15
 * File creation time: 10:51
 * Project: tb4
 */


interface AccountTransactionTypeRepositoryInterface
{
    // VALID TRANSACTION TYPE NAMES
    const TYPE_TOURNAMENT_DOLLARS     = 'tournamentdollars';
    const TYPE_TOURNAMENT_WIN         = 'tournamentwin';
    const TYPE_PAYPAL_DEPOSIT         = 'paypaldeposit';
    const TYPE_BET_WIN                = 'betwin';
    const TYPE_BET_ENTRY              = 'betentry';
    const TYPE_EWAY_DEPOSIT           = 'ewaydeposit';
    const TYPE_BANK_DEPOSIT           = 'bankdeposit';
    const TYPE_BPAY_DEPOSIT           = 'bpaydeposit';
    const TYPE_ADMIN                  = 'admin';
    const TYPE_TESTING                = 'testing';
    const TYPE_ENTRY                  = 'entry';
    const TYPE_BUY_IN                 = 'buyin';
    const TYPE_BET_REFUND             = 'betrefund';
    const TYPE_CHARGE_BACK            = 'chargeback';
    const TYPE_PROMO                  = 'promo';
    const TYPE_MONEYBOOKERS_DEPOSIT   = 'moneybookersdeposit';
    const TYPE_TOURNAMENT_REFUND      = 'tournamentrefund';
    const TYPE_POLI_DEPOSIT           = 'polideposit';
    const TYPE_WITHDRAWAL             = 'withdrawal';
    const TYPE_BETWIN_CANCELLED       = 'betwincancelled';
    const TYPE_DORMANT_CHARGE         = 'dormantcharge';
    const TYPE_PARENT_FUND_ACCOUNT    = 'parentfundaccount';
    const TYPE_CHILD_ACCOUNT_FUNDED   = "childaccountfunded";
    const TYPE_CHILD_FUND_ACCOUNT     = "childfundaccount";
    const TYPE_PARENT_ACCOUNT_FUNDED  = "parentaccountfunded";
    const TYPE_TOURNAMENT_REBUY_BUYIN = "tournamentrebuybuyin";
    const TYPE_TOURNAMENT_REBUY_ENTRY = "tournamentrebuyentry";
    const TYPE_TOURNAMENT_TOPUP_BUYIN = "tournamenttopupbuyin";
    const TYPE_TOURNAMENT_TOPUP_ENTRY = "tournamenttopupentry";
    const TYPE_EWAY_RECURRING_DEPOSIT = 'ewayrecurringdeposit';
    const TYPE_PROMO_TOURNAMENT_ENTRY = "promotournamententry";

    public function getTransactionTypeByKeyword($keyword);

}