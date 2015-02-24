<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/02/2015
 * Time: 2:13 PM
 */

namespace TopBetta\backend;


use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Accounting\WithdrawalService;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\UserAccount\UserFreeCreditService;

class RiskUserAccountController extends \BaseController {

    const RECENT_DEPOSIT_HISTORY_NO = 3;
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

    public function __construct(AccountTransactionService $accountTransactionService,
                                WithdrawalService $withdrawalService,
                                UserFreeCreditService $userFreeCreditService,
                                ApiResponse $apiResponse)
    {
        $this->accountTransactionService = $accountTransactionService;
        $this->withdrawalService = $withdrawalService;
        $this->userFreeCreditService = $userFreeCreditService;
        $this->apiResponse = $apiResponse;
    }

    public function show($id)
    {
        $accountData = array();

        try{
            //get total account balance
            $accountData['account-balance'] = $this->accountTransactionService->getAccountBalanceForUser($id);

            //get free credit balance
            $accountData['free-credit-balance'] = $this->userFreeCreditService->getFreeCreditBalanceForUser($id);

            //get the total deposits made by user
            $accountData['total-deposits'] = $this->accountTransactionService->getTotalDepositsForUser($id);

            //get the last 3 deposits for the user
            $accountData['recent-deposits'] = $this->accountTransactionService->getLastNDepositsForUser(
                $id,
                self::RECENT_DEPOSIT_HISTORY_NO
            )->toArray();

            //get the total withdrawals
            //use withdrawal requests for this value as some withdrawals are under deposit transaction types in the DB
            $accountData['total-withdrawals'] = $this->withdrawalService->getTotalApprovedWithdrawalsForUser($id);

            //get the win - loss for sports betting
            $accountData['win-loss-sports'] = $this->accountTransactionService->getSportsWinLossForUser($id);

            //get the win - loss for racing bets
            $accountData['win-loss-racing'] = $this->accountTransactionService->getRacingWinLossForUser($id);
        } catch ( \Exception $e ) {
            return $this->apiResponse->failed(array($e->getMessage(), $accountData));
        }

        return $this->apiResponse->success($accountData);
    }
}