<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 11:08 AM
 */

namespace TopBetta\Http\Requests\ExternalRequests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exception\HttpResponseException;
use TopBetta\Services\Errors\ExternalAPIError;
use Validator;

class AuthorizedExternalRequest extends FormRequest {

    public function validate()
    {

        $validator = Validator::make($this->all(), $this->rules(), $this->messages());

        if ($validator->fails()) {
            throw new HttpResponseException(
               $this->errorResponse(new ExternalAPIError(ExternalAPIError::ERROR_CODE_VALIDATION_ERROR, $validator->errors()->getMessages()), 400)
            );
        }

        $this->authorize();
    }

    public function authorize()
    {
        if (! \Config::get('externalsource.message_authentication')) {
            return true;
        }

        $authenticationService = \App::make('TopBetta\Services\Affiliates\AffiliateMessageAuthenticationService');

        if (! $authenticationService->authenticateMessage($this->all()) ) {
            throw new HttpResponseException(
                $this->errorResponse(new ExternalAPIError(ExternalAPIError::ERROR_CODE_UNAUTHENTICATED_MESSAGE), 401)
            );
        }
    }


    public function errorResponse(ExternalAPIError $error, $statusCode)
    {
        $response = \App::make('TopBetta\Services\Response\ExternalJsonResponse');

        return $response->createErrorResponse($this->get('affiliate_id'), $error, $statusCode);
    }

    public function rules()
    {
        return array();
    }

    public function messages()
    {
        return array();
    }
}