<?php namespace TopBetta\Http\Backend\Controllers;
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/02/2015
 * Time: 2:13 PM
 */

use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Accounting\WithdrawalService;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\UserAccount\UserAccountService;
use TopBetta\Services\UserAccount\UserFreeCreditService;

class RiskUserAccountController extends \BaseController {

    const RECENT_DEPOSIT_HISTORY_DAYS = 30;

    /**
     * @var AccountTransactionService
     */
    private $accountTransactionService;

    /**
     * @var WithdrawalService
     */
    private $withdrawalService;

    /**
     * @var UserFreeCreditService
     */
    private $userFreeCreditService;
    /**
     * @var ApiResponse
     */
    private $apiResponse;
    /**
     * @var UserAccountService
     */
    private $userService;

    public function __construct(AccountTransactionService $accountTransactionService,
                                WithdrawalService $withdrawalService,
                                UserFreeCreditService $userFreeCreditService,
                                UserAccountService $userService,
                                ApiResponse $apiResponse)
    {
        $this->accountTransactionService = $accountTransactionService;
        $this->withdrawalService = $withdrawalService;
        $this->userFreeCreditService = $userFreeCreditService;
        $this->apiResponse = $apiResponse;
        $this->userService = $userService;
    }

    public function show($id)
    {
        $accountData = array();

        try{
            //get user info
            $accountData['user'] = $this->userService->getTopBettaUser($id);

            //get total account balance
            $accountData['account_balance'] = $this->accountTransactionService->getAccountBalanceForUser($id);

            //get free credit balance
            $accountData['free_credit_balance'] = $this->userFreeCreditService->getFreeCreditBalanceForUser($id);

            //get the total deposits made by user
            $accountData['total_deposits'] = $this->accountTransactionService->getTotalDepositsForUser($id);

            //get the last 3 deposits for the user
            $accountData['recent_deposits'] = $this->accountTransactionService->getRecentDepositsForUser(
                $id,
                self::RECENT_DEPOSIT_HISTORY_DAYS
            )->toArray();

            //get the total withdrawals
            //use withdrawal requests for this value as some withdrawals are under deposit transaction types in the DB
            $accountData['total_withdrawals'] = $this->withdrawalService->getTotalApprovedWithdrawalsForUser($id);

            //get the win - loss for sports betting
            $accountData['win_loss_sports'] = $this->accountTransactionService->getSportsWinLossForUser($id);

            //get the win - loss for racing bets
            $accountData['win_loss_racing'] = $this->accountTransactionService->getRacingWinLossForUser($id);
        } catch ( \Exception $e ) {
            return $this->apiResponse->failed(array($e->getMessage(), $accountData));
        }

        return $this->apiResponse->success($accountData);
    }
}