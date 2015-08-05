<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 1:00 PM
 */

namespace TopBetta\Services\Errors;


class ExternalAPIError {

    // --- VALIDATION ERRORS ---
    const ERROR_CODE_VALIDATION_ERROR = 'V001';

    // --- AUTHORIZATION ERRORS ---
    const ERROR_CODE_UNAUTHENTICATED_MESSAGE = 'A001';

    // --- USER ACCOUNT ERRORS ---
    const ERROR_CODE_CREATION_FAILED = 'U001';
    const ERROR_CODE_ACCOUNT_EXISTS = 'U002';
    const ERROR_CODE_UNAUTHORIZED_ACCOUNT_TYPE = 'U003';
    const ERROR_CODE_USER_NOT_FOUND = 'U004';

    // --- TOKEN ERRORS ---
    const ERROR_CODE_TOKEN_GENERATION = 'T001';
    const ERROR_CODE_INVALID_TOKEN = 'T002';
    const ERROR_CODE_TOKEN_VALIDATION = 'T003';

    // --- MESSAGES ---
    private $errorMessages = array(
        self::ERROR_CODE_UNAUTHENTICATED_MESSAGE => "Invalid message token",
        self::ERROR_CODE_VALIDATION_ERROR => "Invalid request body",
        self::ERROR_CODE_CREATION_FAILED => "User account creation failed",
        self::ERROR_CODE_ACCOUNT_EXISTS => "User account already exists",
        self::ERROR_CODE_UNAUTHORIZED_ACCOUNT_TYPE => "Cannot create this account type",
        self::ERROR_CODE_USER_NOT_FOUND => "User not found",
        self::ERROR_CODE_TOKEN_GENERATION => "Error generation token",
        self::ERROR_CODE_INVALID_TOKEN => "Invalid token",
        self::ERROR_CODE_TOKEN_VALIDATION => "An error occurred validating the token"
    );

    /**
     * @var
     */

    private $error;
    /**
     * @var
     */
    private $data;

    public function __construct($error, $data = array())
    {
        $this->error = $error;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    public function getErrorMessage()
    {
        return array_get($this->errorMessages, $this->error);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

}