<?php

namespace TopBetta\Http\Controllers\External;

use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Requests\ExternalRequests\UserTokenRequest;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Authentication\Exceptions\TokenGenerationException;
use TopBetta\Services\Authentication\TokenAuthenticationService;
use TopBetta\Services\Errors\ExternalAPIError;
use TopBetta\Services\Response\ExternalJsonResponse;
use TopBetta\Services\UserAccount\Exceptions\UserNotFoundException;

class UserTokenController extends Controller
{
    /**
     * @var TokenAuthenticationService
     */
    private $authenticationService;
    /**
     * @var ExternalJsonResponse
     */
    private $response;

    public function __construct(TokenAuthenticationService $authenticationService, ExternalJsonResponse $response)
    {
       $this->authenticationService = $authenticationService;
        $this->response = $response;
    }

    public function requestToken(UserTokenRequest $request)
    {
        try {
            $token = $this->authenticationService->processTokenRequestNoValidation($request['tournament_username']);
        } catch (UserNotFoundException $e) {
            return $this->response->createErrorResponse(
                $request['source_name'],
                new ExternalAPIError(ExternalAPIError::ERROR_CODE_USER_NOT_FOUND, $e->getMessage()),
                400
            );
        } catch (TokenGenerationException $e) {
            return $this->response->createErrorResponse(
                $request['source_name'],
                new ExternalAPIError(ExternalAPIError::ERROR_CODE_TOKEN_GENERATION, $e->getMessage()),
                500
            );
        } catch (\Exception $e) {
            return $this->response->createErrorResponse(
                $request['source_name'],
                new ExternalAPIError(ExternalAPIError::ERROR_CODE_TOKEN_GENERATION, "Unknown error"),
                500
            );
        }

        return $this->response->createResponse($request['source_name'], array("token" => $token));
    }

    public function test()
    {
        return "worked " . \Auth::user()->username;
    }
}
