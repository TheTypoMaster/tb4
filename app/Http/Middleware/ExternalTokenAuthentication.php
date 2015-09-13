<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 2:56 PM
 */

namespace TopBetta\Http\Middleware;


use Cartalyst\Sentry\Users\UserNotFoundException;
use TopBetta\Services\Authentication\Exceptions\InvalidTokenException;
use TopBetta\Services\Authentication\TokenAuthenticationService;
use TopBetta\Services\Errors\ExternalAPIError;
use TopBetta\Services\Response\ExternalJsonResponse;
use TopBetta\Services\Validation\Exceptions\ValidationException;

class ExternalTokenAuthentication {

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

    public function handle($request, \Closure $next)
    {
        if( ! \Auth::user() ) {
            try {
                $this->authenticationService->tokenLoginExternal($request->all());
            } catch (UserNotFoundException $e) {
                return $this->response->createErrorResponse(
                    array_get($request, 'source_name'),
                    new ExternalAPIError(ExternalAPIError::ERROR_CODE_USER_NOT_FOUND, $e->getMessage()),
                    404
                );
            } catch (InvalidTokenException $e) {
                return $this->response->createErrorResponse(
                    array_get($request, 'source_name'),
                    new ExternalAPIError(ExternalAPIError::ERROR_CODE_INVALID_TOKEN, $e->getMessage()),
                    401
                );
            } catch (ValidationException $e) {
                return $this->response->createErrorResponse(
                    array_get($request, 'source_name'),
                    new ExternalAPIError(ExternalAPIError::ERROR_CODE_VALIDATION_ERROR, $e->getErrors()),
                    400
                );
            } catch (\Exception $e) {
                \Log::error('ExternalTokenAuthentication: ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
                return $this->response->createErrorResponse(
                    array_get($request, 'source_name'),
                    new ExternalAPIError(ExternalAPIError::ERROR_CODE_TOKEN_VALIDATION, "Unknown error")
                );
            }
        }

        return $next($request);
    }
}