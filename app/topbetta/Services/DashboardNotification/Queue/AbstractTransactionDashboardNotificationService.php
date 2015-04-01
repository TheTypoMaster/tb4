<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/03/2015
 * Time: 12:23 PM
 */

namespace TopBetta\Services\DashboardNotification\Queue;


use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface as TransactionType;
use TopBetta\Repositories\Contracts\FreeCreditTransactionTypeRepositoryInterface as FreeCreditTransactionType;

abstract class AbstractTransactionDashboardNotificationService extends DashboardNotificationQueueService
{

    //account transaction transforms
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
        TransactionType::TYPE_TESTING               => "testing",
    );

    private $freeCreditTransactionTypeMapping = array(
        FreeCreditTransactionType::TRANSACTION_TYPE_ENTRY           => 'entry_bonus_credit',
        FreeCreditTransactionType::TRANSACTION_TYPE_BUYIN           => 'buyin_bonus_credit',
        FreeCreditTransactionType::TRANSACTION_TYPE_WIN             => 'win_bonus_credit',
        FreeCreditTransactionType::TRANSACTION_TYPE_REFUND          => 'refund_bonus_credit',
        FreeCreditTransactionType::TRANSACTION_TYPE_PROMO           => 'promo_bonus_credit',
        FreeCreditTransactionType::TRANSACTION_TYPE_TESTING         => 'testing_bonus_credit',
        FreeCreditTransactionType::TRANSACTION_TYPE_ADMIN           => 'admin_bonus_credit',
        FreeCreditTransactionType::TRANSACTION_TYPE_PURCHASE        => 'purchase_bonus_credit',
        FreeCreditTransactionType::TRANSACTION_TYPE_REFERRAL        => 'referral_bonus_credit',
        FreeCreditTransactionType::TRANSACTION_TYPE_NO_QUALIFIERS   => 'noqualifiers_bonus_credit',
        FreeCreditTransactionType::TRANSACTION_TYPE_FREE_BET_ENTRY  => 'freebetentry_bonus_credit',
        FreeCreditTransactionType::TRANSACTION_TYPE_FREE_BET_REFUND => 'freebetrefund_bonus_credit',
    );

    /**
     * @var
     */
    private $accountTransactionRepository;

    abstract public function getTransaction($transactionId);

    abstract public function getFreeCreditTransaction($transactionId);

    public function formatTransactions($transactionIds, $freeCredit = false)
    {
        $payload = array();

        foreach($transactionIds as $transaction) {
            $payload[] = $this->formatTransaction($freeCredit ? $this->getFreeCreditTransaction($transaction) : $this->getTransaction($transaction), $freeCredit);
        }

        return $payload;
    }

    public function formatTransaction($transaction, $freeCredit = false)
    {
        if ( ! count($transaction) ) {
            return array();
        }

        $transactionPayload = array(
            'transaction_date'      => array_get($transaction, 'created_date', null),
            "transaction_amount"    => array_get($transaction, 'amount', 0),
            'transaction_type_name' => array_get($freeCredit ? $this->freeCreditTransactionTypeMapping : $this->transactionTypeMapping, array_get($transaction, 'transaction_type.keyword', 0), null),
            "external_id"           => array_get($transaction, 'id', 0),
        );

        $transactionPayload['users'] = count(array_get($transaction, 'giver', array())) ? array($this->formatUser(array_get($transaction, 'giver', array()))) : array();

        //set the user types
        $transactionPayload['transaction_parent_key'] = 'recipient';
        $transactionPayload['transaction_child_key'] = 'giver';

        return $transactionPayload;
    }
}