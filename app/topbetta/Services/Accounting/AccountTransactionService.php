<?php namespace TopBetta\Services\Accounting;
/**
 * Coded by Oliver Shanahan
 * File creation date: 5/01/15
 * File creation time: 10:44
 * Project: tb4
 */

use Carbon\Carbon;
use Validator;

use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;

use TopBetta\Services\Authentication\TokenAuthenticationService;
use TopBetta\Services\UserAccount\UserAccountService;

use TopBetta\Services\Validation\Exceptions\ValidationException;


class AccountTransactionService {

    protected $accounttransactions;
    protected $accounttransactiontypes;
    protected $authentication;
    protected $useraccountservice;
    protected $user;

    public function __construct(AccountTransactionRepositoryInterface $accounttransactions,
                                AccountTransactionTypeRepositoryInterface $accounttransactiontypes,
                                UserRepositoryInterface $user,
                                UserAccountService $useraccountservice,
                                TokenAuthenticationService $authentication)
    {
        $this->accounttransactions = $accounttransactions;
        $this->accounttransactiontypes = $accounttransactiontypes;
        $this->user = $user;
        $this->authentication = $authentication;
        $this->useraccountservice = $useraccountservice;
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


    /**
     * Manages the transfer of funds from a parent account to a child account. Performs source identification, validation and funds transfer
     *
     * @param $input
     * @return array
     * @throws ValidationException
     */
    public function transferFunds(array $input){

        // validation rules
        $rules = array(
            'source' => 'required',
            'parent_user_name' => 'required|alphadash',
            'personal_betting_user_name' => 'required|alphadash',
            'child_betting_user_name' => 'required|alphadash',
            'transfer_amount' => 'required|numeric',
            'token' => 'required'
        );

        // validate input
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) throw new ValidationException("Validation Failed", $validator->messages());

        // confirm source of request
        if(!$this->authentication->checkSource($input)) throw new ValidationException("Validation Failed", 'Source not confirmed');

        // get the parent user account details
        $parentUserDetails = $this->user->getUserDetailsFromUsername($input['parent_user_name']);
        if(!$parentUserDetails) throw new ValidationException("Validation Failed", 'Parent user acccount not found');

        // check that the parent betting account exists
        $childBettingUserDetails = $this->user->getUserDetailsFromUsername($input['child_betting_user_name']);
        if(!$childBettingUserDetails) throw new ValidationException("Validation Failed", 'Betting user not found');

        // confirm child account is a child of the parent account
        if(!$this->useraccountservice->confirmBettingAccount($input['parent_user_name'], $input['child_betting_user_name'])) throw new ValidationException("Validation Failed", 'Invalid Payload - child betting account is not a child of the parent account');

        // get parent account balance
        $parentAccountBalance = $this->accounttransactions->getAccountBalanceByUserId($parentUserDetails['id']);

        // make sure there is enough to fund the transfer from the parent account to the child account
        if($parentAccountBalance < $input['transfer_amount']) throw new ValidationException("Validation Failed", 'Insuffcient parent betting funds');

        // remove the funds from the parent account
        $removeFunds = $this->decreaseAccountBalance($parentUserDetails['id'], $input['transfer_amount'], 'clubfundaccount');
        if (!$removeFunds) throw new ValidationException("Validation Failed", 'Failed to decrease parent account');

        // increase child account
        $addFunds = $this->increaseAccountBalance($childBettingUserDetails['id'], $input['transfer_amount'], 'bettingfundaccount');
        if (!$addFunds) throw new ValidationException("Validation Failed", 'Failed to increase child betting account');

        return $addFunds;

    }



}