<?php namespace TopBetta\Http\Controllers\Frontend;

use Auth;
use Log;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Models\PaymentEwayTokens;
use TopBetta\Resources\UserResource;
use TopBetta\Services\Accounting\Payments\EwayPaymentService;
use TopBetta\Services\Response\ApiResponse;

class EwayCreditCardController extends Controller {

    /**
     * @var EwayPaymentService
     */
    private $ewayPaymentService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(EwayPaymentService $ewayPaymentService, ApiResponse $response)
    {
        $this->beforeFilter('token.auth');
        $this->ewayPaymentService = $ewayPaymentService;
        $this->response = $response;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Auth::user();

        if ($user instanceof UserResource) {
            $user = $user->getModel();
        }

        //get the card details from Eway
        $cards = array();
        foreach($user->ewayTokens as $token) {
            try {
                $cards[] = $this->ewayPaymentService->getCard($token->cc_token);
            } catch (\Exception $e) {
                Log::error("Eway Token ERROR fetching card details for token " . $token->cc_token ." with message " . $e->getMessage());
            }
        }

        Log::info("EWAY RESPONSE: " . print_r($this->response->success($cards), true));

        return $this->response->success($cards);
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
		//
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
        //check the specified credit card token exists
        if (PaymentEwayTokens::checkTokenExists(\Auth::user()->id, $id)) {
            //delete the token
            $paymentToken = PaymentEwayTokens::where("cc_token", "=", $id)->first();

            if( $paymentToken->scheduledPayments->count() ) {
                return $this->response->failed("Card is linked to recurring payments. Please cancel these first before removing card", 400);
            }

            $paymentToken->delete();

            return $this->response->success("Card removed");
        } else {
            return $this->response->failed("Card not found", 404);
        }
	}


}
