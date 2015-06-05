<?php

namespace TopBetta\Frontend;

use Auth;
use Input;
use TopBetta\Repositories\Contracts\ScheduledPaymentRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Services\Accounting\Payments\Exceptions\PaymentException;
use TopBetta\Services\Accounting\ScheduledDepositService;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Validation\Exceptions\ValidationException;

class ScheduledDepositsController extends \BaseController {

    /**
     * @var ScheduledDepositService
     */
    private $scheduledDepositService;
    /**
     * @var ApiResponse
     */
    private $response;
    /**
     * @var ScheduledPaymentRepositoryInterface
     */
    private $paymentRepositoryInterface;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(ScheduledDepositService $scheduledDepositService, ScheduledPaymentRepositoryInterface $paymentRepositoryInterface, UserRepositoryInterface $userRepository, ApiResponse $response)
    {
        $this->beforeFilter('token.auth');
        $this->scheduledDepositService = $scheduledDepositService;
        $this->response = $response;
        $this->paymentRepositoryInterface = $paymentRepositoryInterface;
        $this->userRepository = $userRepository;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        if( $childUsername = Input::get('child_username', null) ) {
            $user = $this->userRepository->getUserByUsername($childUsername);
        } else {
            $user = Auth::user();
        }

        $payments = $this->scheduledDepositService->getActivePaymentsForUser($user, Input::get('source', null));

        return $this->response->success($payments);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::json()->all();

        try {
            $response = $this->scheduledDepositService->createSchedule(Auth::user(), $input);
        } catch ( ValidationException $e ) {
            return $this->response->failed($e->getErrors());
        } catch ( PaymentException $e ) {
            return $this->response->failed($e->getMessage());
        }

        return $this->response->success("Schedule Created");
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $payment = $this->paymentRepositoryInterface->find($id);

        if( $payment->payment_token->user->id != Auth::user()->id ) {
            return $this->response->failed("Payment does not belong to user", 403);
        }

		try {
            $this->scheduledDepositService->cancelPayment($payment);
        } catch ( \Exception $e ) {
            return $this->failed($e->getMessage());
        }

        return $this->response->success("Payment cancelled");
	}


}
