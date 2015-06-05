<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/03/2015
 * Time: 12:23 PM
 */

namespace TopBetta\Services\ExternalSourceNotifications\Queue;


use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface as TransactionType;
use TopBetta\Repositories\Contracts\FreeCreditTransactionTypeRepositoryInterface as FreeCreditTransactionType;

abstract class AbstractTransactionExternalSourceNotificationService extends ExternalSourceNotificationQueueService
{

    //account transaction transforms
    private $transactionTypeMapping = array(
        TransactionType::TYPE_TOURNAMENT_DOLLARS     => "tournament_dollars",
        TransactionType::TYPE_TOURNAMENT_WIN         => "tournament_win",
        TransactionType::TYPE_BET_ENTRY              => "bet_placement",
        TransactionType::TYPE_BET_WIN                => "bet_win",
        TransactionType::TYPE_PAYPAL_DEPOSIT         => "paypal_deposit",
        TransactionType::TYPE_EWAY_DEPOSIT           => "eway_deposit",
        TransactionType::TYPE_BANK_DEPOSIT           => "bank_deposit",
        TransactionType::TYPE_BPAY_DEPOSIT           => "bpay_deposit",
        TransactionType::TYPE_ENTRY                  => "tournament_entry",
        TransactionType::TYPE_BUY_IN                 => "tournament_buy_in",
        TransactionType::TYPE_BET_REFUND             => "bet_refund",
        TransactionType::TYPE_CHARGE_BACK            => "chargeback",
        TransactionType::TYPE_PROMO                  => "promo",
        TransactionType::TYPE_TOURNAMENT_REFUND      => "tournament_refund",
        TransactionType::TYPE_BETWIN_CANCELLED       => "bet_win_cancelled",
        TransactionType::TYPE_WITHDRAWAL             => "withdrawal",
        TransactionType::TYPE_PARENT_FUND_ACCOUNT    => "parent_fund_child_account",
        TransactionType::TYPE_CHILD_ACCOUNT_FUNDED   => "child_account_funded",
        TransactionType::TYPE_CHILD_FUND_ACCOUNT     => "child_fund_parent_account",
        TransactionType::TYPE_PARENT_ACCOUNT_FUNDED  => "parent_account_funded",
        TransactionType::TYPE_DORMANT_CHARGE         => "dormant_charge",
        TransactionType::TYPE_TESTING                => "testing",
        TransactionType::TYPE_ADMIN                  => "admin",
        TransactionType::TYPE_TOURNAMENT_REBUY_BUYIN => "tournament_rebuy_buyin",
        TransactionType::TYPE_TOURNAMENT_REBUY_ENTRY => "tournament_rebuy_entry",
        TransactionType::TYPE_TOURNAMENT_TOPUP_BUYIN => "tournament_topup_buyin",
        TransactionType::TYPE_TOURNAMENT_TOPUP_ENTRY => "tournament_topup_entry",
        TransactionType::TYPE_EWAY_RECURRING_DEPOSIT => "eway_recurring_deposit",
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
        FreeCreditTransactionType::TRANSACTION_TYPE_REBUY_BUYIN     => "tournament_rebuy_buyin_bonus_credit",
        FreeCreditTransactionType::TRANSACTION_TYPE_REBUY_ENTRY     => "tournament_rebuy_entry_bonus_credit",
        FreeCreditTransactionType::TRANSACTION_TYPE_TOPUP_BUYIN     => "tournament_topup_buyin_bonus_credit",
        FreeCreditTransactionType::TRANSACTION_TYPE_TOPUP_ENTRY     => "tournament_topup_entry_bonus_credit",
    );

    abstract public function getTransaction($transactionId);

    abstract public function getFreeCreditTransaction($transactionId);

    public function formatTransactions($transactionIds, $freeCredit = false)
    {
        $payload = array();

        foreach ($transactionIds as $transaction) {
            $payload[] = $this->formatTransaction($freeCredit ? $this->getFreeCreditTransaction($transaction) : $this->getTransaction($transaction), $freeCredit);
        }

        return $payload;
    }

    public function formatTransaction($transaction, $freeCredit = false, $suffix = "")
    {
        if (!count($transaction)) {
            return array();
        }

        $transactionPayload = array(
            'transaction_date'   => array_get($transaction, 'created_date', null),
            "transaction_amount" => abs(array_get($transaction, 'amount', 0)),
            "external_id"        => array_get($transaction, 'id', 0),
            'transaction_type'   => null,
            "user"               => null,
        );

        if ($transactionType = array_get($transaction, 'transaction_type', null)) {
            //get transaction type name mapped to dashboard values
            $transactionTypeName = array_get($freeCredit ? $this->freeCreditTransactionTypeMapping : $this->transactionTypeMapping, array_get($transactionType, 'keyword', 0), null);

            //format transaction type
            $transactionPayload['transaction_type'] = array(
                "transaction_type_name"         => $transactionTypeName ? $suffix ? $transactionTypeName . "_" . $suffix : $transactionTypeName : null,
                "transaction_type_description"  => array_get($transactionType, 'description', null),
                "transaction_type_bonus_credit" => $freeCredit,
            );
        }

        $transactionPayload['user'] = count(array_get($transaction, 'giver', array())) ? $this->formatUser(array_get($transaction, 'giver', array())) : array();

        return $transactionPayload;
    }
}