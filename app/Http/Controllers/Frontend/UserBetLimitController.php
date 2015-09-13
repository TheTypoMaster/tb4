<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\UserAccount\UserAccountService;

class UserBetLimitController extends Controller
{

    /**
     * @var UserAccountService
     */
    private $accountService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(UserAccountService $accountService, ApiResponse $response)
    {
        $this->accountService = $accountService;
        $this->response = $response;
    }

    public function setBetLimit(Request $request)
    {
        if( ! ($amount = $request->get('amount')) ||  $amount < 0 ) {
            return $this->response->failed("Invalid amount", 400);
        }

        try {
            $response = $this->accountService->setBetLimit(\Auth::user(), $amount);
        } catch (\InvalidArgumentException $e) {
            return $this->response->failed($e->getMessage(), 400);
        } catch (\Exception $e) {
            \Log::error("UserBetLimitController: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown error");
        }

        return $this->response->success(\Lang::get('bets.'.$response));
    }

    public function removeBetLimit()
    {
        try {
            $this->accountService->removeBetLimit(\Auth::user());
        } catch (\InvalidArgumentException $e) {
            return $this->response->failed($e->getMessage());
        } catch (\Exception $e) {
            \Log::error("UserBetLimitController: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown error");
        }

        return $this->response->success(\Lang::get('bets.' . UserAccountService::BET_LIMIT_REQUESTED));
    }

    public function getBetLimit()
    {
        $user = \Auth::user();

        return $this->response->success(array(
            "bet_limit" => $user->topbettauser->bet_limit,
            "requested_bet_limit" => $user->topbettauser->requested_bet_limit
        ));
    }
}
