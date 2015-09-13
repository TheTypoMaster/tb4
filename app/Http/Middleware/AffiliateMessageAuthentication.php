<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 9:53 AM
 */

namespace TopBetta\Http\Middleware;

use Closure;
use TopBetta\Services\Affiliates\AffiliateMessageAuthenticationService;

/**
 * Middleware for external routes
 * Class AffiliateMessageAuthentication
 * @package TopBetta\Http\Middleware
 */
class AffiliateMessageAuthentication {

    /**
     * @var AffiliateMessageAuthenticationService
     */
    private $authenticationService;

    public function __construct(AffiliateMessageAuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function handle($request, Closure $next)
    {
        if (! $this->authenticationService->authenticateMessage($request->all())) {
            return response("Unauthorized.", 401);
        }

        return $next($request);
    }
}