<?php namespace TopBetta\Services\Accounting;
/**
 * Coded by Oliver Shanahan
 * File creation date: 5/01/15
 * File creation time: 10:44
 * Project: tb4
 */

use Carbon\Carbon;
use Validator;

use TopBetta\Repositories\Contracts\BetOriginRepositoryInterface;
use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Services\Authentication\TokenAuthenticationService;
use TopBetta\Services\UserAccount\UserAccountService;
use TopBetta\Services\Validation\Exceptions\ValidationException;

class AccountTransactionService {

    //Deposit transaction types
    public static $depositTransactions  = array(
         AccountTransactionTypeRepositoryInterface::TYPE_PAYPAL_DEPOSIT,
         AccountTransactionTypeRepositoryInterface::TYPE_EWAY_DEPOSIT,
         AccountTransactionTypeRepositoryInterface::TYPE_BPAY_DEPOSIT,
         AccountTransactionTypeRepositoryInterface::TYPE_BANK_DEPOSIT,
         AccountTransactionTypeRepositoryInterface::TYPE_POLI_DEPOSIT,
         AccountTransactionTypeRepositoryInterface::TYPE_MONEYBOOKERS_DEPOSIT,
     );

    protected $accounttransactions;
    protected $accounttransactiontypes;
    protected $authentication;
    protected $useraccountservice;
    protected $betOriginRepository;
    protected $user;

    /**
     * Save the bet origin Ids after we fetch them once
     * @var array
     */
    protected $betOrigins = array();


    public function __construct(AccountTransactionRepositoryInterface $accounttransactions,
                                AccountTransactionTypeRepositoryInterface $accounttransactiontypes,
                                UserRepositoryInterface $user,
                                UserAccountService $useraccountservice,
                                TokenAuthenticationService $authentication,
                                BetOriginRepositoryInterface $betOriginRepository)
    {
        $this->accounttransactions = $accounttransactions;
        $this->accounttransactiontypes = $accounttransactiontypes;
        $this->user = $user;
        $this->authentication = $authentication;
        $this->useraccountservice = $useraccountservice;
        $this->betOriginRepository = $betOriginRepository;
    }

    public function increaseAccountBalance($userID, $amount, $keyword, $giverId = -1, $desc = null, $transactionDate = null){

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
        // $giverId = -1;
        $recipient_id = $userID;

        if($recipient_id == null) {
            $recipient_id = $giverId;
        }

        $params = array(
            'recipient_id' 				=> $recipient_id,
            'giver_id' 					=> $giverId,
            'session_tracking_id' 		=> $tracking_id,
            'amount' 					=> $amount,
            'notes' 					=> $desc,
            'account_transaction_type_id' 	=> $transactionTypeDetails['id'],
            'created_date'              => $transactionDate ? $transactionDate : Carbon::now('Australia/Sydney')

        );

        return $this->accounttransactions->create($params);
    }

    public function decreaseAccountBalance($userID, $amount, $keyword, $giverId = -1, $desc = null, $transactionDate = null){
        return $this->increaseAccountBalance($userID, -$amount, $keyword, $giverId, $desc, $transactionDate);
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

        // check that the child betting account exists
        $childBettingUserDetails = $this->user->getUserDetailsFromUsername($input['child_betting_user_name']);
        if(!$childBettingUserDetails) throw new ValidationException("Validation Failed", 'Betting user not found');

        // confirm child account is a child of the parent account
        if(!$this->useraccountservice->confirmBettingAccount($input['parent_user_name'], $input['child_betting_user_name'])) throw new ValidationException("Validation Failed", 'Invalid Payload - child betting account is not a child of the parent account');

        // get parent account balance
        $parentAccountBalance = $this->accounttransactions->getAccountBalanceByUserId($parentUserDetails['id']);

        // make sure there is enough to funds the transfer from the parent account to the child account
        if($parentAccountBalance < $input['transfer_amount']) throw new ValidationException("Validation Failed", 'Insuffcient parent betting funds');

        // remove the funds from the parent account

        $removeFunds = $this->decreaseAccountBalance($parentUserDetails['id'], $input['transfer_amount'], 'parentfundaccount', $parentUserDetails['id']);
        if (!$removeFunds) throw new ValidationException("Validation Failed", 'Failed to decrease parent account');

        // increase child account
        $addFunds = $this->increaseAccountBalance($childBettingUserDetails['id'], $input['transfer_amount'], 'childaccountfunded', $parentUserDetails['id']);

       
        if (!$addFunds) throw new ValidationException("Validation Failed", 'Failed to increase child betting account');

        return $addFunds;

    }

    /**
     * Return un-used funds from child betting account back to the parent
     *
     * @param $input
     * @return array
     * @throws ValidationException
     */

    public function returnFunds($input){

        // validation rules
        $rules = array(
            'source' => 'required',
            'parent_user_name' => 'required',
            'token' => 'required'
        );

        // validate input
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) throw new ValidationException("Validation Failed", $validator->messages());

        // confirm source of request
        if(!$this->authentication->checkSource($input)) throw new ValidationException("Validation Failed", 'Source not confirmed');

        // check and get the parent user account details
        $parentUserDetails = $this->user->getUserDetailsFromUsername($input['parent_user_name']);
        if(!$parentUserDetails) throw new ValidationException("Validation Failed", 'Parent user acccount not found');

        // get the child accounts
        $childUserAccounts = $this->user->getChildUserAccounts($parentUserDetails['id']);
        if(!$childUserAccounts) throw new ValidationException("Validation Failed", 'No child acccounts found');

        //dd(count($childUserAccounts));
        // remove funds fom child accounts
        // $returnArray = array();
        foreach($childUserAccounts as $childAccount){

            // get current account balance of child account
            $childAccountBalance = $this->accounttransactions->getAccountBalanceByUserId($childAccount['id']);

            // remove funds from child account
            if($childAccountBalance > 0) $removedFunds = $this->decreaseAccountBalance($childAccount['id'], $childAccountBalance, 'childfundaccount');

            if(isset($removedFunds)){
                $details = array();
                $details['user_name'] = $childAccount['username'];
                $details['amount'] = $childAccountBalance;

                // add funds to parent account
                $addFunds = $this->increaseAccountBalance($parentUserDetails['id'], $childAccountBalance, 'parentaccountfunded');
               // if (!$addFunds) throw new ValidationException("Validation Failed", 'Failed to increase child betting account');
            }
        }

        // return username and cents
        return $returnArray = array($details);

    }

    public function chargeDormantAccounts($dormantDays, $dormantChargeDate, $dormantAmount, $transactionDate)
    {
        //get the transaction type
        $dormantTransactionType = $this->accounttransactiontypes->getTransactionTypeByKeyword(
            AccountTransactionTypeRepositoryInterface::TYPE_DORMANT_CHARGE
        );

        //Get the dormant users
        $dormantUsers = $this->user->getDormantUsersWIthNoDormantChargeAfter($dormantTransactionType['id'], $dormantDays, $dormantChargeDate);

        foreach ($dormantUsers as $user) {
            //charge dormant accounts
            if ( ($balance = $this->getAccountBalanceForUser($user->id)) > 0) {
                //charge either dormantAmount or whole balance if balance is < dormantAmount
                $this->decreaseAccountBalance(
                    $user->id,
                    min($balance, $dormantAmount),
                    AccountTransactionTypeRepositoryInterface::TYPE_DORMANT_CHARGE,
                    -1,
                    null,
                    $transactionDate
                );
            }
        }
    }

    public function getTransaction($transactionId)
    {
        return $this->accounttransactions->find($transactionId);
    }

    public function getAccountBalanceForUser($userId)
    {
        return $this->accounttransactions->getAccountBalanceByUserId($userId);
    }


    public function getAvailableWithdrawalBalance($userId)
    {
        return $this->getAccountBalanceForUser($userId) - $this->useraccountservice->getBalanceToTurnOver($userId);
	}
	
    public function getAccountTransactionsForUserPaginated($userId)
    {
        return $this->accounttransactions->getUserTransactionsPaginated($userId);
    }

    public function isDepositTransaction($transactionType)
    {
        return in_array($transactionType, self::$depositTransactions);
    }

    public function getTotalDepositsForUser($userId)
    {
        //get positive deposit transactions only
        return $this->accounttransactions->getTotalOnlyPositiveTransactionsForUserByTypeIn(
            $userId,
            $this->getTransactionTypeIds(self::$depositTransactions)
        );
    }

    /**
     * Gets all deposits for user in the last $daysPrevious
     * @param $userId
     * @param $daysPrevious
     * @return mixed
     */
    public function getRecentDepositsForUser($userId, $daysPrevious)
    {
        return $this->accounttransactions->getRecentPositiveTransactionsForUserByTypeIn(
            $userId,
            Carbon::now()->subDays($daysPrevious)->toDateTimeString(),
            $this->getTransactionTypeIds(self::$depositTransactions)
        );
    }

    /**
     * @param $userId
     * @param $n
     * @return \Illuminate\Database\Eloquent\Collection;
     */
    public function getLastNDepositsForUser($userId, $n)
    {
        return $this->accounttransactions->getLastNPositiveTransactionsForUserByTypeIn(
            $userId,
            $n,
            $this->getTransactionTypeIds(self::$depositTransactions)
        );
    }
    
    public function getRacingWinLossForUser($userId)
    {
        //racing win loss = wins - losses + refunds
       //$a = $this->getTotalRacingBetsForUser($userId);
        //dd(\DB::getQueryLog());
        return $this->getTotalRacingBetsForUser($userId) +
            $this->getTotalRacingBetWinsForUser($userId) + 
            $this->getTotalRacingBetRefundForUser($userId);
    }

    public function getSportsWinLossForUser($userId)
    {
        //sports win loss = wins - losses + refunds
        return $this->getTotalSportsBetsForUser($userId) +
            $this->getTotalSportsBetWinsForUser($userId) +
            $this->getTotalSportsBetRefundForUser($userId);
    }

    // --- BETTING TRANSACTION VALUES ---

    /**
    * Gets total spent on sports bet for a user
    * Returns negative amount!
    * @param $userId
    * @return mixed
    */
    public function getTotalSportsBetsForUser($userId)
    {
        return $this->accounttransactions->getTotalBetTransactionsForUserByOrigin(
            $userId,
            array($this->getBetOriginId(BetOriginRepositoryInterface::ORIGIN_SPORTS_BETTING))
        );
    }

    public function getTotalSportsBetWinsForUser($userId)
    {
        return $this->accounttransactions->getTotalBetWinTransactionsForUserByOrigin(
            $userId,
            array($this->getBetOriginId(BetOriginRepositoryInterface::ORIGIN_SPORTS_BETTING))
        );
    }

    public function getTotalSportsBetRefundForUser($userId)
    {
        return $this->accounttransactions->getTotalBetRefundTransactionsForUserByOrigin(
            $userId,
            array($this->getBetOriginId(BetOriginRepositoryInterface::ORIGIN_SPORTS_BETTING))
        );
    }

    /**
     * Gets total spent on racing bets for a user
     * Returns negative amount!
     * @param $userId
     * @return mixed
     */
    public function getTotalRacingBetsForUser($userId)
    {
        return $this->accounttransactions->getTotalBetTransactionsForUserByOrigin(
            $userId,
            array(
                $this->getBetOriginId(BetOriginRepositoryInterface::ORIGIN_RACE_BETTING),
                $this->getBetOriginId(BetOriginRepositoryInterface::ORIGIN_TOURNAMENT)
            )
        );
    }

    public function getTotalRacingBetWinsForUser($userId)
    {
        return $this->accounttransactions->getTotalBetWinTransactionsForUserByOrigin(
            $userId,
            array(
                $this->getBetOriginId(BetOriginRepositoryInterface::ORIGIN_RACE_BETTING),
                $this->getBetOriginId(BetOriginRepositoryInterface::ORIGIN_TOURNAMENT)
            )
        );
    }

    public function getTotalRacingBetRefundForUser($userId)
    {
        return $this->accounttransactions->getTotalBetRefundTransactionsForUserByOrigin(
            $userId,
            array(
                $this->getBetOriginId(BetOriginRepositoryInterface::ORIGIN_RACE_BETTING),
                $this->getBetOriginId(BetOriginRepositoryInterface::ORIGIN_TOURNAMENT)
            )
        );
    }

    /**
     * Converts array of account transaction type names
     *  to array of account transaction type ids
     * @param $types
     * @return array
     */
    private function getTransactionTypeIds($types)
    {
        $transactionTypeRepo = $this->accounttransactiontypes;

        return array_map(function($transactionType) use ($transactionTypeRepo) {
            return $transactionTypeRepo->getTransactionTypeByKeyword($transactionType)['id'];
        }, $types);
    }

    private function getBetOriginId($betOriginName)
    {
        if( ! isset($this->betOrigins[$betOriginName]) ) {
            $this->betOrigins[$betOriginName] = $this->betOriginRepository->getOriginByKeyWord($betOriginName)['id'];
        }

        return $this->betOrigins[$betOriginName];
    }

}