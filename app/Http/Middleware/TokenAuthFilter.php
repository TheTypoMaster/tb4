<?php namespace TopBetta\Http\Middleware;
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/06/2015
 * Time: 2:37 PM
 */

use Auth;
use Input;
use Response;
use TopBetta\Services\Authentication\TokenAuthenticationService;

class TokenAuthFilter {

    /**
     * @var TokenAuthenticationService
     */
    private $authenticationService;

    public function __construct(TokenAuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function filter()
    {
        if( ! Auth::user() ) {
            try {
                \Log::info("TOKEN LOGIN INPUT: " . print_r(Input::all(), true));
                $user = $this->authenticationService->tokenLogin(Input::only(array("token", "username", "parent_username", "source")));
            } catch (\Exception $e) {
                \Log::error("UNAUTHED: " . $e->getMessage() . $e->getErrors());
                return Response::json(array("error" => "true", "message" => "Unauthorized Access " . $e->getMessage(), "errors" => $e->getErrors()), 401);
            }
        }
    }

}