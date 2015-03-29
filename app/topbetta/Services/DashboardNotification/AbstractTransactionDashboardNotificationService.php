<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/03/2015
 * Time: 12:23 PM
 */

namespace TopBetta\Services\DashboardNotification;


use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface as TransactionType;

abstract class AbstractTransactionDashboardNotificationService extends DashboardNotificationQueueService
{

    private $transactionTypeMapping = array(
        TransactionType::TYPE_TOURNAMENT_DOLLARS    => "tournament_dollars",
        TransactionType::TYPE_TOURNAMENT_WIN        => "tournament_win",
        TransactionType::TYPE_BET_ENTRY             => "bet_placement",
        TransactionType::TYPE_BET_WIN               => "bet_win",
        TransactionType::TYPE_PAYPAL_DEPOSIT        => "paypal_deposit",
        TransactionType::TYPE_EWAY_DEPOSIT          => "eway_deposit",
        TransactionType::TYPE_BANK_DEPOSIT          => "bank_deposit",
        TransactionType::TYPE_BPAY_DEPOSIT          => "bpay_deposit",
        TransactionType::TYPE_ENTRY                 => "tournament_entry",
        TransactionType::TYPE_BUY_IN                => "tournament_buy_in",
        TransactionType::TYPE_BET_REFUND            => "bet_refund",
        TransactionType::TYPE_CHARGE_BACK           => "chargeback",
        TransactionType::TYPE_PROMO                 => "promo",
        TransactionType::TYPE_TOURNAMENT_REFUND     => "tournament_refund",
        TransactionType::TYPE_BETWIN_CANCELLED      => "bet_win_cancelled",
        TransactionType::TYPE_WITHDRAWAL            => "withdrawal",
        TransactionType::TYPE_PARENT_FUND_ACCOUNT   => "parent_fund_child_account",
        TransactionType::TYPE_CHILD_ACCOUNT_FUNDED  => "child_account_funded",
        TransactionType::TYPE_CHILD_FUND_ACCOUNT    => "child_fund_parent_account",
        TransactionType::TYPE_PARENT_ACCOUNT_FUNDED => "parent_account_funded",
        TransactionType::TYPE_DORMANT_CHARGE        => "dormant_charge",
    );

    public function formatTransaction($transaction)
    {
        if ( ! count($transaction) ) {
            return array();
        }

        return array(
            "transaction_amount"    => array_get($transaction, 'amount', 0),
            'transaction_type_name' => array_get($this->transactionTypeMapping, array_get($transaction, 'transaction_type.keyword', 0), null),
            "external_id"           => array_get($transaction, 'id', 0),
        );
    }
}