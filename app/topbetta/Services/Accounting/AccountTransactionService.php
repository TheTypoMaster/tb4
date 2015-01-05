<?php namespace TopBetta\Services\Accounting;
/**
 * Coded by Oliver Shanahan
 * File creation date: 5/01/15
 * File creation time: 10:44
 * Project: tb4
 */

use Carbon\Carbon;

use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface;

class AccountTransactionService {

    protected $accounttransactions;
    protected $accounttransactiontypes;

    public function __construct(AccountTransactionRepositoryInterface $accounttransactions,
                                AccountTransactionTypeRepositoryInterface $accounttransactiontypes)
    {
        $this->accounttransactions = $accounttransactions;
        $this->accounttransactiontypes = $accounttransactiontypes;
    }

    public function increaseAccountBalance($userID, $amount, $keyword, $desc = null){

        // get the transaction type details for the keyword
        $transactionTypeDetails = $this->accounttransactiontypes->getTransactionTypeByKeyword($keyword);

        if(!$transactionTypeDetails) {
            return false;
        }

        // if description is not passed in then use the default one
        if($desc == null) {
            $desc = $transactionTypeDetails['description'];
        }

        $tracking_id = -1;
        $giver_id = -1;
        $recipient_id = $userID;

        if($recipient_id == null) {
            $recipient_id = $giver_id;
        }

        $params = array(
            'recipient_id' 				=> $recipient_id,
            'giver_id' 					=> $giver_id,
            'session_tracking_id' 		=> $tracking_id,
            'amount' 					=> $amount,
            'notes' 					=> $desc,
            'account_transaction_type_id' 	=> $transactionTypeDetails['id'],
            'created_date'              => Carbon::now('Australia/Sydney')

        );

        return $this->accounttransactions->create($params);
    }

    public function decreaseAccountBalance($userID, $amount, $keyword, $desc = null){
        return $this->increaseAccountBalance($userID, -$amount, $keyword, $desc);
    }
}