<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 2:26 PM
 */

namespace TopBetta\Services\Response;


use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use TopBetta\Services\Affiliates\AffiliateMessageAuthenticationService;
use TopBetta\Services\Errors\ExternalAPIError;

class ExternalJsonResponse {

    /**
     * @var AffiliateMessageAuthenticationService
     */
    private $authenticationService;

    public function __construct(AffiliateMessageAuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function createResponse($affiliate, $data, $statusCode = 200, $headers = array(), $options = 0)
    {
        $responseData = array(
            "data" => $data,
            "http_status_code" => $statusCode,
            "timestamp" => Carbon::now()->toDateString()
        );

        $responseData['token'] = $this->authenticationService->createHashedMessage($data, $affiliate);

        return new JsonResponse($responseData, $statusCode, $headers, $options);
    }

    public function createErrorResponse($affiliate, ExternalAPIError $error, $statusCode = 500, $headers = array(), $options = 0) {
        $responseData = array(
            "errors" => $error->getData(),
            "error_code" => $error->getError(),
            "error_message" => $error->getErrorMessage(),
            "http_status_code" => $statusCode,
            "timestamp" => Carbon::now()->toDateTimeString(),
        );

        return new JsonResponse($responseData, $statusCode, $headers, $options);
    }
}