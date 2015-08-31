<?php

namespace TopBetta\Http\Controllers\External;

use TopBetta\Http\Requests\ExternalRequests\CreateTournamentAccountRequest;
use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;

use TopBetta\Services\Response\ExternalJsonResponse;
use TopBetta\Services\Affiliates\AffiliateUserAccountService;
use TopBetta\Services\Affiliates\Exceptions\InvalidAccountTypeException;
use TopBetta\Services\Errors\ExternalAPIError;
use TopBetta\Services\UserAccount\Exceptions\AccountExistsException;
use TopBetta\Services\Validation\Exceptions\ValidationException;

class UserAccountController extends Controller
{
    /**
     * @var AffiliateUserAccountService
     */
    private $accountService;
    /**
     * @var ExternalJsonErrorResponse
     */
    private $response;

    public function __construct(AffiliateUserAccountService $accountService, ExternalJsonResponse $response)
    {
        $this->accountService = $accountService;
        $this->response = $response;
    }

    public function createTournamentAccount(CreateTournamentAccountRequest $request)
    {
        try {
            $user = $this->accountService->createTournamentAccount($request->all());
        } catch (InvalidAccountTypeException $e) {
            $this->response->createErrorResponse(
                $request['source_name'], new ExternalAPIError(ExternalAPIError::ERROR_CODE_UNAUTHORIZED_ACCOUNT_TYPE), 401
            );
        }catch (AccountExistsException $e) {
            $this->response->createErrorResponse(
                $request['source_name'], new ExternalAPIError(ExternalAPIError::ERROR_CODE_ACCOUNT_EXISTS), 400
            );
        } catch (ValidationException $e) {
            $this->response->createErrorResponse(
                $request['source_name'], new ExternalAPIError(ExternalAPIError::ERROR_CODE_VALIDATION_ERROR, $e->getErrors()), 400
            );
        } catch (\Exception $e) {
            \Log::error("UserAccountController: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->createErrorResponse(
                $request['source_name'], new ExternalAPIError(ExternalAPIError::ERROR_CODE_CREATION_FAILED, "Unknown error occurred"), 500
            );
        }

        return $this->response->createResponse($request['source_name'], $user);
    }
}
